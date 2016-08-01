<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\Setting\CreateNew;
use App\Command\Setting\DeleteAll;
use App\Command\Setting\DeleteOne;
use App\Command\Setting\UpdateOne;
use App\Entity\Setting as SettingEntity;
use App\Repository\SettingInterface;
use App\Validator\Setting as SettingValidator;
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
     * @param App\Repository\SettingInterface $repository
     * @param App\Validator\Setting           $validator
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
     * Creates a new child Setting.
     *
     * @param App\Command\Setting\CreateNew $command
     *
     * @return App\Entity\Setting
     */
    public function handleCreateNew(CreateNew $command) : SettingEntity {
        $this->validator->assertSectionName($command->section);
        $this->validator->assertPropName($command->property);
        $this->validator->assertId($command->companyId);

        $setting = $this->repository->create(
            [
                'section'    => $command->section,
                'property'   => $command->property,
                'value'      => $command->value,
                'company_id' => $command->companyId,
                'created_at' => time()
            ]
        );

        $this->repository->save($setting);

        return $setting;
    }

    /**
     * Updates a Setting.
     *
     * @param App\Command\Setting\UpdateOne $command
     *
     * @return App\Entity\Setting
     */
    public function handleUpdateOne(UpdateOne $command) : SettingEntity {
        $this->validator->assertId($command->companyId);
        $this->validator->assertPropName($command->propNameId);
        $this->validator->assertSectionName($command->sectionNameId);

        $setting = $this->repository->findOne($command->companyId, $command->sectionNameId, $command->propNameId);

        if ($command->value) {
            $setting->value     = $command->value;
            $setting->updatedAt = time();
        }

        $success = $this->repository->update($setting);

        return $success ? $setting : false;
    }

    /**
     * Deletes all settings ($command->companyId).
     *
     * @param App\Command\Setting\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

    /**
     * Deletes a Setting.
     *
     * @param App\Command\Setting\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->companyId);
        $this->validator->assertPropName($command->property);
        $this->validator->assertSectionName($command->section);

        return $this->repository->deleteOne($command->companyId, $command->section, $command->property);
    }
}
