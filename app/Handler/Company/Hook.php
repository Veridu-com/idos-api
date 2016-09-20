<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Hook\CreateNew;
use App\Command\Company\Hook\DeleteOne;
use App\Command\Company\Hook\GetOne;
use App\Command\Company\Hook\UpdateOne;
use App\Entity\Company\Hook as HookEntity;
use App\Event\Company\Hook\Created;
use App\Event\Company\Hook\Deleted;
use App\Event\Company\Hook\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\HookInterface;
use App\Validator\Company\Hook as HookValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;
use App\Handler\HandlerInterface;

/**
 * Handles Hook commands.
 */
class Hook implements HandlerInterface {
    /**
     * Hook Repository instance.
     *
     * @var App\Repository\Company\HookInterface
     */
    protected $repository;
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\Company\CredentialInterface
     */
    protected $credentialRepository;
    /**
     * Hook Validator instance.
     *
     * @var App\Validator\Company\Hook
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
            return new \App\Handler\Company\Hook(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Hook'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Credential'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Hook'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\Company\HookInterface       $repository
     * @param App\Repository\Company\CredentialInterface $repository
     * @param App\Validator\Company\Hook                 $validator
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
     * @param App\Command\Company\Hook\CreateNew $command
     *
     * @see App\Repository\DBHook::findByPubKey
     * @see App\Repository\DBHook::create
     * @see App\Repository\DBHook::save
     *
     * @throws App\Exception\Validate\HookException
     * @throws App\Exception\NotFound\HookException
     * @throws App\Exception\Create\HookException
     *
     * @return App\Entity\Hook
     */
    public function handleCreateNew(CreateNew $command) : HookEntity {
        try {
            $this->validator->assertTriggerName($command->trigger);
            $this->validator->assertUrl($command->url);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->companyId) {
            throw new NotFound\Company\HookException('Company not found', 404);
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
            throw new Create\Company\HookException('Error while trying to create a hook', 500, $e);
        }

        return $hook;
    }

    /**
     * Updates a hook.
     *
     * @param App\Command\Company\Hook\UpdateOne $command
     *
     * @see App\Repository\DBHook::findByPubKey
     * @see App\Repository\DBHook::find
     * @see App\Repository\DBHook::save
     *
     * @throws App\Exception\Validate\HookException
     * @throws App\Exception\NotFound\HookException
     * @throws App\Exception\Update\HookException
     *
     * @return App\Entity\Hook
     */
    public function handleUpdateOne(UpdateOne $command) : HookEntity {
        try {
            $this->validator->assertId($command->hookId);
            $this->validator->assertTriggerName($command->trigger);
            $this->validator->assertUrl($command->url);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->companyId) {
            throw new NotFound\Company\HookException('Company not found', 404);
        }

        $hook       = $this->repository->find($command->hookId);
        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($hook->credential_id != $credential->id) {
            throw new NotFound\Company\HookException('Credential not found', 404);
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
            throw new Update\Company\HookException('Error while trying to update a hook', 500, $e);
        }

        return $hook;
    }

    /**
     * Deletes a hook.
     *
     * @param App\Command\Company\Hook\DeleteOne $command
     *
     * @see App\Repository\DBHook::findByPubKey
     * @see App\Repository\DBHook::find
     * @see App\Repository\DBHook::delete
     *
     * @throws App\Exception\Validate\HookException
     * @throws App\Exception\NotFound\HookException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->hookId);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->companyId) {
            throw new NotFound\Company\HookException('Company not found', 404);
        }

        $hook       = $this->repository->find($command->hookId);
        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($hook->credential_id != $credential->id) {
            throw new NotFound\Company\HookException('Credential not found', 404);
        }

        $rowsAffected = $this->repository->delete($command->hookId);

        if (! $rowsAffected) {
            throw new NotFound\Company\HookException('No hooks found for deletion', 404);
        }

        $event = new Deleted($hook);
        $this->emitter->emit($event);
    }

    /**
     * Gets one Hook.
     *
     * @param App\Command\Company\Hook\GetOne $command
     *
     * @see App\Repository\DBHook::findByPubKey
     * @see App\Repository\DBHook::find
     *
     * @throws App\Exception\NotFound\HookException
     *
     * @return int
     */
    public function handleGetOne(GetOne $command) : HookEntity {
        $this->validator->assertId($command->hookId);

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);
        $hook       = $this->repository->find($command->hookId);

        if ($credential->id != $hook->credentialId || $credential->companyId != $command->companyId) {
            throw new NotFound\Company\HookException('Company not found', 404);
        }

        return $hook;
    }
}
