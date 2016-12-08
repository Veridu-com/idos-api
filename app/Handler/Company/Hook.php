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
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\HookInterface;
use App\Validator\Company\Hook as HookValidator;
use GuzzleHttp\Client as HttpClient;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Hook commands.
 */
class Hook implements HandlerInterface {
    /**
     * Hook Repository instance.
     *
     * @var \App\Repository\Company\HookInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Hook Validator instance.
     *
     * @var \App\Validator\Company\Hook
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
     * HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

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
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter'),
                $container
                    ->get('httpClient')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\Company\HookInterface       $repository
     * @param \App\Repository\Company\CredentialInterface $repository
     * @param \App\Validator\Company\Hook                 $validator
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
     * @param \GuzzleHttp\Client                          $httpClient
     *
     * @return void
     */
    public function __construct(
        HookInterface $repository,
        CredentialInterface $credentialRepository,
        HookValidator $validator,
        Event $eventFactory,
        Emitter $emitter,
        HttpClient $httpClient
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->validator            = $validator;
        $this->eventFactory         = $eventFactory;
        $this->emitter              = $emitter;
        $this->httpClient           = $httpClient;
    }

    /**
     * Gets one Hook.
     *
     * @param \App\Command\Company\Hook\GetOne $command
     *
     * @see \App\Repository\DBHook::findByPubKey
     * @see \App\Repository\DBHook::find
     *
     * @throws \App\Exception\NotFound\Company\HookException
     *
     * @return int
     */
    public function handleGetOne(GetOne $command) : HookEntity {
        try {
            $this->validator->assertId($command->hookId);
            $this->validator->assertCompany($command->company);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);
        $hook       = $this->repository->find($command->hookId);

        if ($credential->id != $hook->credentialId || $credential->companyId != $command->company->id) {
            throw new NotFound\Company\HookException('Company not found', 404);
        }

        return $hook;
    }

    /**
     * Creates a new hook.
     *
     * @param \App\Command\Company\Hook\CreateNew $command
     *
     * @see \App\Repository\DBHook::findByPubKey
     * @see \App\Repository\DBHook::create
     * @see \App\Repository\DBHook::save
     *
     * @throws \App\Exception\Validate\Company\HookException
     * @throws \App\Exception\NotFound\Company\HookException
     * @throws \App\Exception\Create\Company\HookException
     *
     * @return \App\Entity\Company\Hook
     */
    public function handleCreateNew(CreateNew $command) : HookEntity {
        try {
            $this->validator->assertTriggerName($command->trigger);
            $this->validator->assertUrl($command->url);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertCompany($command->company);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
            throw new NotFound\Company\HookException('Company not found', 404);
        }

        $validResponse = false;
        try {
            if ($this->httpClient->request('GET', $command->url)->getStatusCode() === 204) {
                $validResponse = true;
            }
        } catch (\Exception $e) {
        }

        if (! $validResponse) {
            throw new Create\Company\HookException('Failed to perform hook handshake.', 500);
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
            $event = $this->eventFactory->create('Company\\Hook\\Created', $hook, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\HookException('Error while trying to create a hook', 500, $e);
        }

        return $hook;
    }

    /**
     * Updates a hook.
     *
     * @param \App\Command\Company\Hook\UpdateOne $command
     *
     * @see \App\Repository\DBHook::findByPubKey
     * @see \App\Repository\DBHook::find
     * @see \App\Repository\DBHook::save
     *
     * @throws \App\Exception\Validate\Company\HookException
     * @throws \App\Exception\NotFound\Company\HookException
     * @throws \App\Exception\Update\Company\HookException
     *
     * @return \App\Entity\Company\Hook
     */
    public function handleUpdateOne(UpdateOne $command) : HookEntity {
        try {
            $this->validator->assertId($command->hookId);
            $this->validator->assertTriggerName($command->trigger);
            $this->validator->assertUrl($command->url);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertCompany($command->company);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
            throw new NotFound\Company\HookException('Company not found', 404);
        }

        $hook       = $this->repository->find($command->hookId);
        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($hook->credential_id != $credential->id) {
            throw new NotFound\Company\HookException('Credential not found', 404);
        }

        $validResponse = false;
        try {
            if ($this->httpClient->request('GET', $command->url)->getStatusCode() === 204) {
                $validResponse = true;
            }
        } catch (\Exception $e) {
        }

        if (! $validResponse) {
            throw new Update\Company\HookException('Failed to perform hook handshake.', 500);
        }

        $hook->trigger    = $command->trigger;
        $hook->url        = $command->url;
        $hook->subscribed = $command->subscribed;
        $hook->updatedAt  = time();

        try {
            $hook  = $this->repository->save($hook);
            $event = $this->eventFactory->create('Company\\Hook\\Updated', $hook, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Company\HookException('Error while trying to update a hook', 500, $e);
        }

        return $hook;
    }

    /**
     * Deletes a hook.
     *
     * @param \App\Command\Company\Hook\DeleteOne $command
     *
     * @see \App\Repository\DBHook::findByPubKey
     * @see \App\Repository\DBHook::find
     * @see \App\Repository\DBHook::delete
     *
     * @throws \App\Exception\Validate\Company\HookException
     * @throws \App\Exception\NotFound\Company\HookException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->hookId);
            $this->validator->assertIdentity($command->identity);
            $this->validator->assertCompany($command->company);
        } catch (ValidationException $e) {
            throw new Validate\Company\HookException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId != $command->company->id) {
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

        $event = $this->eventFactory->create('Company\\Hook\\Deleted', $hook, $command->identity);
        $this->emitter->emit($event);
    }
}
