<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Member\CreateNew;
use App\Command\Company\Member\DeleteOne;
use App\Command\Company\Member\UpdateOne;
use App\Entity\Company\Member as MemberEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\MemberInterface;
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
     * @var \App\Repository\Company\MemberInterface
     */
    private $repository;
    /**
     * Member Validator instance.
     *
     * @var \App\Validator\Company\Member
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
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Company\Member(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Member'),
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
     * @param \App\Repository\Company\MemberInterface $repository
     * @param \App\Validator\Member                   $validator
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     *
     * @return void
     */
    public function __construct(
        MemberInterface $repository,
        MemberValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->validator            = $validator;
        $this->eventFactory         = $eventFactory;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a new child Member ($command->companyId).
     *
     * @param \App\Command\Company\Member\CreateNew $command
     *
     * @throws \App\Exception\Validate\Company\MemberException
     * @throws \App\Exception\Create\Company\MemberException
     *
     * @return \App\Entity\Company\Member
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
     * Updates a single Company Member.
     *
     * @param App\Command\Company\Member\UpdateOne $command
     *
     * @throws App\Exception\Validate\MemberException
     * @throws App\Exception\Create\MemberException
     *
     * @return App\Entity\Member
     */
    public function handleUpdateOne(UpdateOne $command) : MemberEntity {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertShortName($command->role);
            $this->validator->assertId($command->memberId);
        } catch (ValidationException $e) {
            throw new Validate\Company\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $member = $this->repository->find($command->memberId);

        // updates entity
        $member->role = $command->role;

        try {
            // persists entity
            $member = $this->repository->save($member);
            $event  = $this->eventFactory->create('Company\\Member\\Updated', $member);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\MemberException('Error while trying to create a member', 500, $e);
        }

        return $member;
    }

    /**
     * Deletes a Member.
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
            $this->validator->assertCompany($command->company);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertId($command->memberId);
        } catch (ValidationException $e) {
            throw new Validate\Company\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $member       = $this->repository->find($command->memberId);
        $rowsAffected = $this->repository->delete($command->memberId);

        if (! $rowsAffected) {
            throw new NotFound\Company\MemberException('No invitations found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Member\\Deleted', $member);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
