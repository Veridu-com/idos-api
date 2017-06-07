<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Invitation\CreateNew;
use App\Command\Company\Invitation\DeleteOne;
use App\Command\Company\Invitation\UpdateOne;
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
     * @var \App\Repository\Company\InvitationInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Company Repository instance.
     *
     * @var \App\Repository\CompanyInterface
     */
    private $companyRepository;
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\Company\SettingInterface
     */
    private $settingRepository;
    /**
     * Invitation Validator instance.
     *
     * @var \App\Validator\Company\Invitation
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Handler\Company\Invitation(
                $repositoryFactory
                    ->create('Company\Invitation'),
                $repositoryFactory
                    ->create('Company\Credential'),
                $repositoryFactory
                    ->create('Company'),
                $repositoryFactory
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
     * @param \App\Repository\Company\InvitationInterface $repository
     * @param \App\Repository\Company\CredentialInterface $credentialRepository
     * @param \App\Repository\CompanyInterface            $companyRepository
     * @param \App\Repository\Company\SettingInterface    $settingRepository
     * @param \App\Validator\Company\Invitation           $validator
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
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
     * @param \App\Command\Company\Invitation\CreateNew $command
     *
     * @throws \App\Exception\Validate\Company\InvitationException
     * @throws \App\Exception\Create\Company\InvitationException
     *
     * @return \App\Entity\Company\Invitation
     */
    public function handleCreateNew(CreateNew $command) : InvitationEntity {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertIdentity($command->identity, 'identity');
            $this->validator->assertName($command->credentialPubKey, 'credentialPubKey');
            $this->validator->assertString($command->name, 'name');
            $this->validator->assertEmail($command->email, 'email');
            $this->validator->assertNullableDate($command->expires, 'expires');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\Company\InvitationException(
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
            throw new Validate\Company\InvitationException('Invalid expiration date. Min: 1 day, Max: 7 days from today');
        }

        $credential           = $this->credentialRepository->findByPubKey($command->credentialPubKey);
        $dashboardNameSetting = $this->settingRepository->findByCompanyIdSectionAndProperties(
            $credential->companyId,
            'company.details',
            [
                'dashboardName'
            ]
        )->first();

        $dashboardName = (! $dashboardNameSetting) ? sprintf('%s idOS Dashboard', $command->company->name) : $dashboardNameSetting->value;
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
            $event      = $this->eventFactory->create(
                'Company\\Invitation\\Created',
                $invitation,
                $credential,
                $command->company->name,
                $dashboardName,
                $signupHash,
                $command->identity
            );
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\InvitationException('Error while trying to create an invitation', 500, $e);
        }

        return $invitation;
    }

    /**
     * Updates a invitation for a future member.
     *
     * @param \App\Command\Company\Invitation\UpdateOne $command
     *
     * @throws \App\Exception\Validate\Company\InvitationException
     * @throws \App\Exception\Create\Company\InvitationException
     *
     * @return \App\Entity\Company\Invitation
     */
    public function handleUpdateOne(UpdateOne $command) : InvitationEntity {
        try {
            $this->validator->assertId($command->invitationId, 'invitationId');
            $this->validator->assertNullableDate($command->expires, 'expires');
        } catch (ValidationException $e) {
            throw new Validate\Company\InvitationException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $invitation = $this->repository->find($command->invitationId);

        if ($command->expires) {
            $expires         = strftime('%Y-%m-%d', strtotime($command->expires));
            $now             = time();
            $expiresDateTime = new \DateTime($expires);
            $today           = new \DateTime(strftime('%Y-%m-%d', $now));
            $diff            = $today->diff($expiresDateTime);

            if ($diff->days < 1 || $diff->days > 7) {
                throw new Validate\Company\InvitationException('Invalid expiration date. Min: 1 day, Max: 7 days from today');
            }

            // updates entity
            $invitation->expires = $expires;
        }

        $credential           = $this->credentialRepository->find($invitation->credentialId);
        $company              = $this->companyRepository->find($invitation->companyId);
        $dashboardNameSetting = $this->settingRepository->findByCompanyIdSectionAndProperties($credential->companyId, 'company.details', ['dashboardName'])->first();

        $dashboardName = (! $dashboardNameSetting) ? sprintf('%s idOS Dashboard', $company->name) : $dashboardNameSetting->value;
        $signupHash    = $invitation->hash;

        try {
            $invitation = $this->repository->save($invitation);

            $this->emitter->emit(
                $this->eventFactory->create(
                    'Company\\Invitation\\Updated',
                    $invitation,
                    $credential,
                    $company->name,
                    $dashboardName,
                    $signupHash
                )
            );

            if ($command->resendEmail) {
                $this->emitter->emit(
                    $this->eventFactory->create(
                        'Company\\Invitation\\Resend',
                        $invitation,
                        $credential,
                        $company->name,
                        $dashboardName,
                        $signupHash
                    )
                );
            }
        } catch (\Exception $e) {
            throw new Create\Company\InvitationException('Error while trying to create an invitation', 500, $e);
        }

        return $invitation;
    }

    /**
     * Deletes an Invitation.
     *
     * @param \App\Command\Company\Invitation\DeleteOne $command
     *
     * @throws \App\Exception\Validate\Company\InvitationException
     * @throws \App\Exception\NotFound\Company\InvitationException
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertId($command->invitationId, 'invitationId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\Company\InvitationException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $invitation = $this->repository->find($command->invitationId);

        if ($invitation->memberId) {
            // cascades to invitations table
            $rowsAffected = $this->repository->delete($invitation->memberId);
        } else {
            $rowsAffected = $this->repository->delete($command->invitationId);
        }

        if (! $rowsAffected) {
            throw new NotFound\Company\InvitationException('No invitations found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Invitation\\Deleted', $invitation, $command->identity);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
