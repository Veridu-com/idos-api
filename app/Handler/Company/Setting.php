<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Setting\CreateNew;
use App\Command\Company\Setting\DeleteOne;
use App\Command\Company\Setting\GetOne;
use App\Command\Company\Setting\ListAll;
use App\Command\Company\Setting\UpdateOne;
use App\Entity\Company\Setting as SettingEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\SettingInterface;
use App\Validator\Company\Setting as SettingValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Setting commands.
 */
class Setting implements HandlerInterface {
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\Company\SettingInterface
     */
    private $repository;
    /**
     * Setting Validator instance.
     *
     * @var \App\Validator\Company\Setting
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
            return new \App\Handler\Company\Setting(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Setting'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Setting'),
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
     * @param \App\Repository\Company\SettingInterface $repository
     * @param \App\Validator\Company\Setting           $validator
     * @param \App\Factory\Event                       $eventFactory
     * @param \League\Event\Emitter                    $emitter
     *
     * @return void
     */
    public function __construct(
        SettingInterface $repository,
        SettingValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * List all Settings.
     *
     * @param \App\Command\Company\Setting\ListAll $command
     *
     * @see \App\Repository\DBSetting::getAllByCompanyId
     * @see \App\Repository\DBSetting::getAllPublicByCompanyId
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleListAll(ListAll $command) : array {
        $this->validator->assertCompany($command->company);
        $this->validator->assertArray($command->queryParams);

        if ($command->hasParentAccess) {
            return $this->repository->getByCompanyId($command->company->id, $command->queryParams);
        }

        // returns filtering by "protected" = false
        return $this->repository->getPublicByCompanyId($command->company->id, $command->queryParams);
    }

    /**
     * Gets one Setting.
     *
     * @param \App\Command\Company\Setting\GetOne $command
     *
     * @see \App\Repository\DBSetting::findOneByCompanyAndId
     *
     * @throws \App\Exception\NotAllowed
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleGetOne(GetOne $command) : SettingEntity {
        $this->validator->assertIdentity($command->identity);
        $this->validator->assertCompany($command->company);
        $this->validator->assertId($command->settingId);

        $setting = $this->repository->findOneByCompanyAndId($command->company->id, $command->settingId);

        if ($setting->protected && ! $command->hasParentAccess) {
            throw new NotAllowed('Not allowed to access this Setting.');
        }

        return $setting;
    }

    /**
     * Creates a new child Setting.
     *
     * @param \App\Command\Company\Setting\CreateNew $command
     *
     * @throws \App\Exception\Validate\Company\SettingException
     * @throws \App\Exception\Create\Company\SettingException
     *
     * @return \App\Entity\Company\Setting
     */
    public function handleCreateNew(CreateNew $command) : SettingEntity {
        try {
            $this->validator->assertMediumName($command->section);
            $this->validator->assertMediumName($command->property);
            $this->validator->assertId($command->company->id);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\SettingException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $setting = $this->repository->create(
            [
                'section'    => $command->section,
                'property'   => $command->property,
                'value'      => $command->value,
                'protected'  => (bool) $command->protected,
                'company_id' => $command->company->id,
                'created_at' => time()
            ]
        );

        try {
            $setting = $this->repository->save($setting);
            $event   = $this->eventFactory->create('Company\\Setting\\Created', $setting, $command->company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\SettingException('Error while trying to create a setting', 500, $e);
        }

        return $setting;
    }

    /**
     * Updates a Setting.
     *
     * @param \App\Command\Company\Setting\UpdateOne $command
     *
     * @throws \App\Exception\Validate\Company\SettingException
     * @throws \App\Exception\Update\Company\SettingException
     *
     * @see \App\Repository\DBSetting::find
     * @see \App\Repository\DBSetting::save
     *
     * @return \App\Entity\Company\Setting
     */
    public function handleUpdateOne(UpdateOne $command) : SettingEntity {
        try {
            $this->validator->assertId($command->settingId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\SettingException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $setting = $this->repository->find($command->settingId);

        $setting->value     = $command->value;
        $setting->updatedAt = time();

        try {
            $setting = $this->repository->save($setting);
            $event   = $this->eventFactory->create('Company\\Setting\\Updated', $setting, $command->company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Company\SettingException('Error while trying to update a setting', 500, $e);
        }

        return $setting;
    }

    /**
     * Deletes a Setting.
     *
     * @param \App\Command\Company\Setting\DeleteOne $command
     *
     * @throws \App\Exception\Validate\Company\SettingException
     * @throws \App\Exception\NotFound\Company\SettingException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->settingId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\SettingException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $setting = $this->repository->find($command->settingId);

        $rowsAffected = $this->repository->delete($command->settingId);

        if (! $rowsAffected) {
            throw new NotFound\Company\SettingException('No settings found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Setting\\Deleted', $setting, $command->company, $command->identity);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all settings ($command->companyId).
     *
     * @param \App\Command\Company\Setting\DeleteAll $command
     *
     * @throws \App\Exception\Validate\Company\SettingException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\SettingException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $settings = $this->repository->findByCompanyId($command->companyId);

        $rowsAffected = $this->repository->deleteByCompanyId($command->companyId);

        $event = $this->eventFactory->create('Company\\Setting\\DeletedMulti', $settings, $command->company, $command->identity);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
