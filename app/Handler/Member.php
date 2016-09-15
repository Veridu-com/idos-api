<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Member\CreateNew;
use App\Command\Member\DeleteAll;
use App\Command\Member\DeleteOne;
use App\Command\Member\UpdateOne;
use App\Entity\Member as MemberEntity;
use App\Event\Member\Created;
use App\Event\Member\Deleted;
use App\Event\Member\DeletedMulti;
use App\Event\Member\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\CredentialInterface;
use App\Repository\MemberInterface;
use App\Repository\UserInterface;
use App\Validator\Member as MemberValidator;
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
     * @var App\Repository\MemberInterface
     */
    protected $repository;
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\CredentialInterface
     */
    protected $credentialRepository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $userRepository;
    /**
     * Member Validator instance.
     *
     * @var App\Validator\Member
     */
    protected $validator;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Member(
                $container
                    ->get('repositoryFactory')
                    ->create('Member'),
                $container
                    ->get('repositoryFactory')
                    ->create('Credential'),
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('Member'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\MemberInterface     $repository
     * @param App\Repository\CredentialInterface $repository
     * @param App\Validator\Member               $validator
     *
     * @return void
     */
    public function __construct(
        MemberInterface $repository,
        CredentialInterface $credentialRepository,
        UserInterface $userRepository,
        MemberValidator $validator,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->userRepository       = $userRepository;
        $this->validator            = $validator;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a new child Member ($command->companyId).
     *
     * @param App\Command\Member\CreateNew $command
     *
     * @return App\Entity\Member
     */
    public function handleCreateNew(CreateNew $command) : MemberEntity {
        try {
            $this->validator->assertUserName($command->userName);
            $this->validator->assertName($command->role);
        } catch (ValidationException $e) {
            throw new Validate\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credential);

        $user = $this->userRepository->findOneBy(
            [
                'username'      => $command->userName,
                'credential_id' => $credential->id
            ]
        );

        $member = $this->repository->create(
            [
                'user_id'    => $user->id,
                'role'       => $command->role,
                'company_id' => $credential->companyId,
                'created_at' => time()
            ]
        );

        try {
            $member = $this->repository->save($member);
            $event  = new Created($member);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\MemberException('Error while trying to create a member', 500, $e);
        }

        $member->relations['user'] = $user;

        return $member;
    }

    /**
     * Updates a Member.
     *
     * @param App\Command\Member\UpdateOne $command
     *
     * @return App\Entity\Member
     */
    public function handleUpdateOne(UpdateOne $command) : MemberEntity {
        try {
            $this->validator->assertId($command->memberId);
            $this->validator->assertName($command->role);
        } catch (ValidationException $e) {
            throw new Validate\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $member            = $this->repository->findOne($command->memberId);
        $member->role      = $command->role;
        $member->updatedAt = time();

        try {
            $member = $this->repository->saveOne($member);
            $event  = new Updated($member);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\MemberException('Error while trying to update a member', 500, $e);
        }

        return $member;
    }

    /**
     * Deletes a Member.
     *
     * @param App\Command\Member\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->memberId);
        } catch (ValidationException $e) {
            throw new Validate\MemberException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $member       = $this->repository->findOne($command->memberId);
        $rowsAffected = $this->repository->delete($command->memberId);

        if (! $rowsAffected) {
            throw new NotFound\MemberException('No members found for deletion', 404);
        }

        $event = new Deleted($member);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all members ($command->companyId).
     *
     * @param App\Command\Member\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $members = $this->repository->getAllByCompanyId($command->companyId);

        $rowsAffected = $this->repository->deleteByCompanyId($command->companyId);

        $event = new DeletedMulti($members);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
