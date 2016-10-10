<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Member\CreateNew;
use App\Command\Company\Member\CreateNewInvitation;
use App\Command\Company\Member\DeleteAll;
use App\Command\Company\Member\DeleteInvitation;
use App\Entity\Company\Member as MemberEntity;
use App\Entity\Company\Invitation as InvitationEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\InvitationInterface;
use App\Repository\Company\MemberInterface;
use App\Repository\UserInterface;
use App\Validator\Company\Member as MemberValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Member commands.
 */
class Member implements HandlerInterface {
    /**
     * Member Repository instance.
     *
     * @var App\Repository\Company\MemberInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Member Validator instance.
     *
     * @var App\Validator\Company\Member
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
            return new \App\Handler\Company\Member(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Member'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Credential'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Invitation'),
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Member'),
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
     * @param App\Repository\Company\MemberInterface     $repository
     * @param App\Repository\Company\CredentialInterface $repository
     * @param App\Repository\Company\InvitationInterface $repository
     * @param App\Validator\Member               $validator
     * @param App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter              $emitter
     *
     * @return void
     */
    public function __construct(
        MemberInterface $repository,
        CredentialInterface $credentialRepository,
        InvitationInterface $invitationRepository,
        UserInterface $userRepository,
        MemberValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->invitationRepository = $invitationRepository;
        $this->userRepository       = $userRepository;
        $this->validator            = $validator;
        $this->eventFactory         = $eventFactory;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a new child Member ($command->companyId).
     *
     * @param App\Command\Company\Member\CreateNew $command
     *
     * @throws App\Exception\Validate\MemberException
     * @throws App\Exception\Create\MemberException
     *
     * @return App\Entity\Member
     */
    public function handleCreateNew(CreateNew $command) : MemberEntity {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertShortName($command->role);
        } catch (ValidationException $e) {
            throw new Validate\Company\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $member = $this->repository->create(
            [
                'identity_id' => $command->identityId,
                'role'        => $command->role,
                'company_id'  => $command->company->id
            ]
        );

        try {
            $member = $this->repository->save($member);
            $event  = $this->eventFactory->create('Company\\Member\\Created', $member);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\MemberException('Error while trying to create a member', 500, $e);
        }

        return $member;
    }

    /**
     * Creates a new invitation for a future member.
     *
     * @param App\Command\Company\Member\CreateNewInvitation $command
     *
     * @throws App\Exception\Validate\MemberException
     * @throws App\Exception\Create\MemberException
     *
     * @return App\Entity\Member
     */
    public function handleCreateNewInvitation(CreateNewInvitation $command) : InvitationEntity {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertName($command->credentialPubKey);
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

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);
        $expires = strftime('%Y-%m-%d', strtotime($command->expires));
        $now = time();
        $expiresDateTime = new \DateTime($expires);
        $today = new \DateTime(strftime('%Y-%m-%d', $now));
        $diff = $today->diff($expiresDateTime);

        if ($diff->days < 1 || $diff->days > 7) {
            throw new Validate\Company\MemberException('Invalid expiration date. Min: 1 day, Max: 7 days from today');
        }

        $invitation = $this->invitationRepository->create(
            [
                'credential_id' => $credential->id,
                'company_id'  => $command->company->id,
                'creator_id'  => $command->identity->id,
                'role'        => $command->role,
                'email'       => $command->email,
                'hash'        => md5($command->email . $command->company->id . microtime()),
                'expires'     => $command->expires ? $expires : strftime('%Y-%m-%d', strtotime('now + 1 days')),
                'created_at'  => $now
            ]
        );

        try {
            $invitation = $this->invitationRepository->save($invitation);
            $event  = $this->eventFactory->create('Company\\Member\\InvitationCreated', $invitation);
            $a = $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\MemberException('Error while trying to create an invitation', 500, $e);
        }

        return $invitation;
    }

    /**
     * Deletes a Member.
     *
     * @param App\Command\Company\Member\DeleteInvitation $command
     *
     * @throws App\Exception\Validate\MemberException
     * @throws App\Exception\NotFound\MemberException
     *
     * @return void
     */
    public function handleDeleteInvitation(DeleteInvitation $command) {
        try {
            $this->validator->assertId($command->invitationId);
        } catch (ValidationException $e) {
            throw new Validate\Company\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $invitation       = $this->invitationRepository->find($command->invitationId);

        if ($invitation->memberId) {
            // cascades to invitations table
            $rowsAffected = $this->repository->delete($invitation->memberId);
        } else {
            $rowsAffected = $this->invitationRepository->delete($command->invitationId);
        }

        if (! $rowsAffected) {
            throw new NotFound\Company\MemberException('No invitations found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Member\\DeletedInvitation', $invitation);
        $this->emitter->emit($event);
    }
}
