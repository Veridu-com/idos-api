<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Invitation\CreateNew;
use App\Command\Company\Invitation\DeleteOne;
use App\Entity\Company\Invitation as InvitationEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\InvitationInterface;
use App\Repository\Company\SettingInterface;
use App\Repository\CompanyInterface;
use App\Validator\Company\Invitation as InvitationValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Invitation commands.
 */
class Invitation implements HandlerInterface {
    /**
     * Invitation Repository instance.
     *
     * @var App\Repository\Company\InvitationInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Invitation Validator instance.
     *
     * @var App\Validator\Company\Invitation
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Company\Invitation(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Invitation'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Credential'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Setting'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Invitation'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\Company\InvitationInterface $repository
     * @param App\Repository\Company\CredentialInterface $credentialRepository
     * @param App\Repository\CompanyInterface            $companyRepository
     * @param App\Repository\Company\SettingInterface    $settingRepository
     * @param App\Validator\Invitation                   $validator
     * @param App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                      $emitter
     *
     * @return void
     */
    public function __construct(
        InvitationInterface $repository,
        CredentialInterface $credentialRepository,
        CompanyInterface $companyRepository,
        SettingInterface $settingRepository,
        InvitationValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->companyRepository    = $companyRepository;
        $this->settingRepository    = $settingRepository;
        $this->validator            = $validator;
        $this->eventFactory         = $eventFactory;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a new invitation for a future member.
     *
     * @param App\Command\Company\Member\CreateNew $command
     *
     * @throws App\Exception\Validate\MemberException
     * @throws App\Exception\Create\MemberException
     *
     * @return \App\Entity\Member
     */
    public function handleCreateNew(CreateNew $command) : InvitationEntity {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertName($command->credentialPubKey);
            $this->validator->assertString($command->name);
            $this->validator->assertEmail($command->email);
            if ($command->expires) {
                $this->validator->assertDate($command->expires);
            }
        } catch (ValidationException $e) {
            throw new Validate\Company\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }
        $expires         = strftime('%Y-%m-%d', strtotime($command->expires));
        $now             = time();
        $expiresDateTime = new \DateTime($expires);
        $today           = new \DateTime(strftime('%Y-%m-%d', $now));
        $diff            = $today->diff($expiresDateTime);

        if ($diff->days < 1 || $diff->days > 7) {
            throw new Validate\Company\MemberException('Invalid expiration date. Min: 1 day, Max: 7 days from today');
        }

        $credential           = $this->credentialRepository->findByPubKey($command->credentialPubKey);
        $dashboardNameSetting = $this->settingRepository->findByCompanyIdSectionAndProperties($credential->companyId, 'company.dashboard', ['name'])->first();

        $dashboardName = (! $dashboardNameSetting) ? sprintf('%s idOS Dashboard', $company->name) : $dashboardNameSetting->value;
        $signupHash    = md5($command->email . $command->company->id . microtime());

        $invitation = $this->repository->create(
            [
                'credential_id' => $credential->id,
                'company_id'    => $command->company->id,
                'creator_id'    => $command->identity->id,
                'name'          => $command->name,
                'email'         => $command->email,
                'role'          => $command->role,
                'hash'          => $signupHash,
                'expires'       => $command->expires ? $expires : strftime('%Y-%m-%d', strtotime('now + 1 days')),
                'created_at'    => $now
            ]
        );

        try {
            $invitation = $this->repository->save($invitation);
            $event      = $this->eventFactory->create('Company\\Invitation\\Created', $invitation, $credential, $command->company->name, $dashboardName, $signupHash);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\MemberException('Error while trying to create an invitation', 500, $e);
        }

        return $invitation;
    }

    /**
     * Deletes an Invitation.
     *
     * @param App\Command\Company\Member\DeleteOne $command
     *
     * @throws App\Exception\Validate\MemberException
     * @throws App\Exception\NotFound\MemberException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->invitationId);
        } catch (ValidationException $e) {
            throw new Validate\Company\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $invitation       = $this->repository->find($command->invitationId);

        if ($invitation->memberId) {
            // cascades to invitations table
            $rowsAffected = $this->repository->delete($invitation->memberId);
        } else {
            $rowsAffected = $this->repository->delete($command->invitationId);
        }

        if (! $rowsAffected) {
            throw new NotFound\Company\MemberException('No invitations found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Invitation\\Deleted', $invitation);
        $this->emitter->emit($event);
    }
}
