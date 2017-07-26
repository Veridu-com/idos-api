<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Recommendation\UpsertOne;
use App\Entity\Profile\Recommendation as RecommendationEntity;
use App\Exception\Create;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\Profile\Recommendation as RecommendationValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Recommendation commands.
 */
class Recommendation implements HandlerInterface {
    /**
     * Recommendation Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Recommendation Validator instance.
     *
     * @var \App\Validator\Profile\Recommendation
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
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Profile\Recommendation(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Recommendation'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Recommendation'),
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
     * @param \App\Repository\RepositoryInterface   $repository
     * @param \App\Validator\Profile\Recommendation $validator
     * @param \App\Factory\Event                    $eventFactory
     * @param \League\Event\Emitter                 $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        RecommendationValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository        = $repository;
        $this->validator         = $validator;
        $this->eventFactory      = $eventFactory;
        $this->emitter           = $emitter;
    }

    /**
     * Creates or updates a recommendation.
     *
     * @param \App\Command\Profile\Recommendation\UpsertOne $command
     *
     * @return \App\Entity\Profile\Recommendation
     */
    public function handleUpsertOne(UpsertOne $command) : RecommendationEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertCredential($command->credential, 'credential');
            $this->validator->assertString($command->result, 'result');
            $this->validator->assertNullableArray($command->passed, 'passed');
            $this->validator->assertNullableArray($command->failed, 'failed');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\RecommendationException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        try {
            $recommendation = $this->repository->create(
                [
                'creator'    => $command->handler->id,
                'user_id'    => $command->user->id,
                'result'     => $command->result,
                'passed'     => $command->passed,
                'failed'     => $command->failed,
                'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $recommendation = $this->repository->upsert(
                $recommendation,
                [
                    'user_id'
                ],
                [
                    'result'     => $recommendation->getRawAttribute('result'),
                    'passed'     => $recommendation->getRawAttribute('passed'),
                    'failed'     => $recommendation->getRawAttribute('failed'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

            if ($recommendation->updatedAt) {
                $event = $this->eventFactory->create(
                    'Profile\Recommendation\Updated',
                    $recommendation,
                    $command->user,
                    $command->handler,
                    $command->company,
                    $command->credential
                );
            } else {
                $event = $this->eventFactory->create(
                    'Profile\Recommendation\Created',
                    $recommendation,
                    $command->user,
                    $command->handler,
                    $command->company,
                    $command->credential
                );
            }

            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\RecommendationException('Error while trying to upsert a recommendation', 404, $exception);
        }

        return $recommendation;
    }
}
