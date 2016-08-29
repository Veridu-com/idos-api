<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Hook\CreateNew;
use App\Command\Hook\DeleteAll;
use App\Command\Hook\DeleteOne;
use App\Command\Hook\UpdateOne;
use App\Entity\Hook as HookEntity;
use App\Event\Hook\Created;
use App\Event\Hook\Deleted;
use App\Event\Hook\DeletedMulti;
use App\Event\Hook\Updated;
use App\Exception\AppException as AppException;
use App\Exception\NotFound;
use App\Repository\CredentialInterface;
use App\Repository\HookInterface;
use App\Validator\Hook as HookValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Hook commands.
 */
class Hook implements HandlerInterface {
    /**
     * Hook Repository instance.
     *
     * @var App\Repository\HookInterface
     */
    protected $repository;
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\CredentialInterface
     */
    protected $credentialRepository;
    /**
     * Hook Validator instance.
     *
     * @var App\Validator\Hook
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
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Hook(
                $container
                    ->get('repositoryFactory')
                    ->create('Hook'),
                $container
                    ->get('repositoryFactory')
                    ->create('Credential'),
                $container
                    ->get('validatorFactory')
                    ->create('Hook'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\HookInterface       $repository
     * @param App\Repository\CredentialInterface $repository
     * @param App\Validator\Hook                 $validator
     *
     * @return void
     */
    public function __construct(
        HookInterface $repository,
        CredentialInterface $credentialRepository,
        HookValidator $validator,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->validator            = $validator;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a new hook.
     *
     * @param App\Command\Hook\CreateNew $command
     *
     * @return App\Entity\Hook
     */
    public function handleCreateNew(CreateNew $command) : HookEntity {
        $this->validator->assertTrigger($command->trigger);
        $this->validator->assertUrl($command->url);

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
            throw new NotFound();
        }

        $hook = $this->repository->create(
            [
                'credential_id' => $credential->id,
                'trigger'       => $command->trigger,
                'url'           => $command->url,
                'subscribed'    => $command->subscribed,
                'created_at'    => time()
            ]
        );

        try {
            $hook  = $this->repository->save($hook);
            $event = new Created($hook);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while trying to create a hook');
        }

        return $hook;
    }

    /**
     * Updates a hook.
     *
     * @param App\Command\Hook\UpdateOne $command
     *
     * @return App\Entity\Hook
     */
    public function handleUpdateOne(UpdateOne $command) : HookEntity {
        $this->validator->assertId($command->hookId);
        $this->validator->assertTrigger($command->trigger);
        $this->validator->assertUrl($command->url);

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
            throw new NotFound();
        }

        $hook = $this->repository->find($command->hookId);

        if ($hook->credential_id != $credential->id) {
            throw new NotFound();
        }

        $hook->trigger    = $command->trigger;
        $hook->url        = $command->url;
        $hook->subscribed = $command->subscribed;
        $hook->updatedAt  = time();

        try {
            $hook  = $this->repository->save($hook);
            $event = new Updated($hook);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while trying to update a hook id ' . $command->hookId);
        }

        return $hook;
    }

    /**
     * Deletes a hook.
     *
     * @param App\Command\Hook\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->hookId);

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
            throw new NotFound();
        }

        $hook = $this->repository->find($command->hookId);

        if ($hook->credential_id != $credential->id) {
            throw new NotFound();
        }

        try {
            $result = $this->repository->delete($command->hookId);
            $event  = new Deleted($hook);
            $this->emitter->emit($event);

            if (! $result) {
                throw new NotFound();
            }
        } catch (\Exception $e) {
            throw new AppException('Error while trying to delete a hook id ' . $command->hookId);
        }

        return $result;
    }

    /**
     * Deletes all hooks from the credential.
     *
     * @param App\Command\Hook\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
            throw new NotFound();
        }

        $hooks = $this->repository->getAllByCredentialId($credential->id);

        try {
            $result = $this->repository->deleteByCredentialId($credential->id);
            $event  = new DeletedMulti($hooks);
            $this->emitter->emit($event);
        } catch (Exception $e) {
            $ids = [];

            foreach ($hooks as $hook) {
                $ids[] = $hook->id;
            }

            throw new AppException('Error while trying to delete hooks id ' . implode(' ', $ids));
        }

        return $result;
    }
}
