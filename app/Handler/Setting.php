<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Handler;

use App\Command\SettingCreateNew;
use App\Command\SettingDeleteAll;
use App\Command\SettingDeleteOne;
use App\Command\SettingUpdateOne;
use App\Repository\SettingInterface;
use App\Validator\Setting as SettingValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;

/**
 * Handles Setting commands.
 */
class Setting implements HandlerInterface {
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\SettingInterface
     */
    protected $repository;
    /**
     * Setting Validator instance.
     *
     * @var App\Validator\Setting
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Setting(
                $container
                    ->get('repositoryFactory')
                    ->create('Setting'),
                $container
                    ->get('validatorFactory')
                    ->create('Setting')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\SettingInterface
     * @param App\Validator\Setting
     *
     * @return void
     */
    public function __construct(
        SettingInterface $repository,
        SettingValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new child Setting ($command->parentId).
     *
     * @param App\Command\SettingCreateNew $command
     *
     * @return array
     */
    public function handleSettingCreateNew(SettingCreateNew $command) {
        $this->validator->assertName($command->name);
        $this->validator->assertParentId($command->parentId);

        $company = $this->repository->create(
            [
                'name'      => $command->name,
                'parent_id' => $command->parentId
            ]
        );

        $company->public_key  = Key::createNewRandomKey()->saveToAsciiSafeString();
        $company->private_key = Key::createNewRandomKey()->saveToAsciiSafeString();

        $this->repository->save($company);

        return $company->toArray();
    }

    /**
     * Updates a Setting.
     *
     * @param App\Command\SettingUpdateOne $command
     *
     * @return array
     */
    public function handleSettingUpdateOne(SettingUpdateOne $command) {
        $this->validator->assertId($command->companyId);
        $this->validator->assertName($command->name);

        $company       = $this->repository->find($command->companyId);
        $company->name = $command->name;

        $this->repository->save($company);

        return $company->toArray();
    }

    /**
     * Deletes a Setting.
     *
     * @param App\Command\SettingDeleteOne $command
     *
     * @return void
     */
    public function handleSettingDeleteOne(SettingDeleteOne $command) {
        $this->validator->assertId($command->companyId);

        $this->repository->deleteById($command->companyId);
    }

    /**
     * Deletes all child Setting ($command->parentId).
     *
     * @param App\Command\DeleteSetting $command
     *
     * @return void
     */
    public function handleSettingDeleteAll(SettingDeleteAll $command) {
        $this->validator->assertId($command->parentId);

        $this->repository->deleteByKey('parent_id', $command->parentId);
    }
}
