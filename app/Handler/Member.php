<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\Member\CreateNew;
use App\Command\Member\DeleteAll;
use App\Command\Member\DeleteOne;
use App\Command\Member\UpdateOne;
use App\Entity\Member as MemberEntity;
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
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Member(
                $container
                    ->get('repositoryFactory')
                    ->create('Member'),
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
     * @param App\Repository\MemberInterface $repository
     * @param App\Repository\UserInterface   $repository
     * @param App\Validator\Member           $validator
     *
     * @return void
     */
    public function __construct(
        MemberInterface $repository,
        UserInterface $userRepository,
        MemberValidator $validator
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->validator      = $validator;
    }

    /**
     * Creates a new child Member ($command->companyId).
     *
     * @param App\Command\Member\CreateNew $command
     *
     * @return App\Entity\Member
     */
    public function handleCreateNew(CreateNew $command) : MemberEntity {
        $this->validator->assertId($command->companyId);
        $this->validator->assertUserName($command->userName);

        $user = $this->userRepository->findOneBy(['username' => $command->userName]);

        $member = $this->repository->create(
            [
                'user_id'    => $user->id,
                'role'       => $command->role,
                'company_id' => $command->companyId,
                'created_at' => time()
            ]
        );

        $member = $this->repository->save($member);

        $member->user = $user->toArray();

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
        $this->validator->assertId($command->companyId);
        $this->validator->assertId($command->userId);

        $member            = $this->repository->findOne($command->companyId, $command->userId);
        $member->role      = $command->role;
        $member->updatedAt = time();

        $member = $this->repository->save($member);

        $member->user = $this->userRepository->find($command->userId)->toArray();

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
        $this->validator->assertId($command->companyId);
        $this->validator->assertId($command->userId);

        return $this->repository->deleteOne($command->companyId, $command->userId);
    }

    /**
     * Deletes all members ($command->companyId).
     *
     * @param App\Command\Member\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

}
