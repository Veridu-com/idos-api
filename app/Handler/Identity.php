<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Identity\CreateNew;
use App\Entity\Identity as IdentityEntity;
use App\Exception\Create;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\IdentityInterface;
use App\Validator\Identity as IdentityValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Identity commands.
 */
class Identity implements HandlerInterface {
    /**
     * Identity Repository instance.
     *
     * @var \App\Repository\IdentityInterface
     */
    private $repository;
    /**
     * Identity Validator instance.
     *
     * @var \App\Validator\Identity
     */
    private $validator;
    /**
     * Event Factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event Emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Identity(
                $container
                    ->get('repositoryFactory')
                    ->create('Identity'),
                $container
                    ->get('validatorFactory')
                    ->create('Identity'),
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
     * @param \App\Repository\IdentityInterface $repository
     * @param \App\Validator\Identity           $validator
     * @param \App\Factory\Event                $eventFactory
     * @param \League\Event\Emitter             $emitter
     *
     * @return void
     */
    public function __construct(
        IdentityInterface $repository,
        IdentityValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates an identity.
     *
     * @param \App\Command\Identity\CreateNew $command
     *
     * @throws \App\Exception\Validate\IdentityException
     * @throws \App\Exception\Create\IdentityException
     *
     * @return \App\Entity\Identity
     */
    public function handleCreateNew(CreateNew $command) : IdentityEntity {
        try {
            $this->validator->assertString($command->profileId, 'profileId');
            $this->validator->assertShortName($command->sourceName, 'sourceName');
            $this->validator->assertString($command->appKey, 'appKey');
        } catch (ValidationException $exception) {
            throw new Validate\IdentityException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $identity = $this->repository->create(
            [
                'reference' => $this->repository->getReference(
                    $command->sourceName,
                    $command->profileId,
                    $command->appKey
                ),
                'created_at' => time()
            ]
        );

        $identity->public_key  = Key::createNewRandomKey()->saveToAsciiSafeString();
        $identity->private_key = Key::createNewRandomKey()->saveToAsciiSafeString();

        try {
            $identity = $this->repository->save($identity);
            $event    = $this->eventFactory->create('Identity\Created', $identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\IdentityException('Error while trying to create an identity', 500, $exception);
        }

        return $identity;
    }
}
