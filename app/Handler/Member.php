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
use App\Repository\CredentialInterface;
use App\Repository\MemberInterface;
use App\Repository\UserInterface;
use App\Validator\Member as MemberValidator;
use Interop\Container\ContainerInterface;

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
                    ->create('Member')
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
        MemberValidator $validator
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->userRepository       = $userRepository;
        $this->validator            = $validator;
    }

    /**
     * Creates a new child Member ($command->companyId).
     *
     * @param App\Command\Member\CreateNew $command
     *
     * @return App\Entity\Member
     */
    public function handleCreateNew(CreateNew $command) : MemberEntity {
        $this->validator->assertUserName($command->userName);

        $credential = $this->credentialRepository->findByPubKey($command->credential);

        $user = $this->userRepository->findOneBy(['username' => $command->userName, 'credential_id' => $credential->id]);

        $member = $this->repository->create(
            [
                'user_id'    => $user->id,
                'role'       => $command->role,
                'company_id' => $credential->companyId,
                'created_at' => time()
            ]
        );

        $member = $this->repository->save($member);

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
        $this->validator->assertId($command->memberId);
        $member            = $this->repository->findOne($command->memberId);
        $member->role      = $command->role;
        $member->updatedAt = time();
        $member            = $this->repository->saveOne($member);

        return $member;
    }

    /**
     * Deletes a Member.
     *
     * @param App\Command\Member\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->memberId);

        return $this->repository->delete($command->memberId);
    }

    /**
     * Deletes all members ($command->companyId).
     *
     * @param App\Command\Member\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        return $this->repository->deleteByCompanyId($command->companyId);
    }
}
