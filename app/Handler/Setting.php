<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Handler;

use App\Command\Setting\CreateNew;
use App\Command\Setting\DeleteAll;
use App\Command\Setting\DeleteOne;
use App\Command\Setting\UpdateOne;
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
     * Creates a new child Setting.
     *
     * @param App\Command\Setting\CreateNew $command
     *
     * @return array
     */
    public function handleCreateNew(CreateNew $command) {
        $this->validator->assertSectionName($command->section);
        $this->validator->assertPropName($command->property);
        $this->validator->assertId($command->companyId);

        $setting = $this->repository->create(
            [
                'section'    => $command->section,
                'property'   => $command->property,
                'value'      => $command->value,
                'company_id' => $command->companyId
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
     * @return array
     */
    public function handleUpdateOne(UpdateOne $command) {
        $this->validator->assertId($command->companyId);
        $this->validator->assertPropName($command->propNameId);
        $this->validator->assertSectionName($command->sectionNameId);

        $setting = $this->repository->findOne($command->companyId, $command->sectionNameId, $command->propNameId);

        // @TODO: dicuss with flavio if we are accepting section & property name changes by user request
        // if ($command->section) {
        //     $setting->section = $command->section;
        // }
        // if ($command->property) {
        //     $setting->property = $command->property;
        // }


        if ($command->value) {
            $setting->value = $command->value;
        }

        $success = $this->repository->update($setting);
        
        return $success ? $setting : false;
    }

    /**
     * Deletes a Setting.
     *
     * @param App\Command\Setting\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        $this->validator->assertId($command->companyId);
        $this->validator->assertPropName($command->property);
        $this->validator->assertSectionName($command->section);

        return $this->repository->deleteOne($command->companyId, $command->section, $command->property);
    }

}
