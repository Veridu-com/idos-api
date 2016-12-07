<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Recommendation\Upsert;
use App\Entity\Profile\Recommendation as RecommendationEntity;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\RecommendationInterface;
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
     * @var \App\Repository\Profile\RecommendationInterface
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
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
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
     * @param \App\Repository\Profile\RecommendationInterface $repository
     * @param \App\Validator\Profile\Recommendation           $validator
     * @param \App\Factory\Event                              $eventFactory
     * @param \League\Event\Emitter                           $emitter
     *
     * @return void
     */
    public function __construct(
        RecommendationInterface $repository,
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
     * Creates or update a recommendation.
     *
     * @param \App\Command\Profile\Recommendation\Upsert $command
     *
     * @return \App\Entity\Profile\Recommendation
     */
    public function handleUpsert(Upsert $command) : RecommendationEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertCompany($command->company);
            $this->validator->assertCredential($command->credential);
            $this->validator->assertString($command->result);
            $this->validator->assertNullableArray($command->reasons);
        } catch (ValidationException $e) {
            throw new Validate\Profile\RecommendationException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $recommendation = $this->repository->create([
                'creator' => $command->service->id,
                'user_id' => $command->user->id,
                'result' => $command->result,
                'reasons' => $command->reasons,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->repository->beginTransaction();
            $this->repository->upsert($recommendation, ['user_id'], [
                'result' => $command->result,
                'reasons' => json_encode($command->reasons),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $recommendation = $this->repository->findOne($command->user->id);
            $this->repository->commit();

            if ($recommendation->updatedAt) {
                $event = $this->eventFactory->create(
                    'Profile\\Recommendation\\Updated',
                    $recommendation,
                    $command->user,
                    $command->service,
                    $command->company,
                    $command->credential
                );
            } else {
                $event = $this->eventFactory->create(
                    'Profile\\Recommendation\\Created',
                    $recommendation,
                    $command->user,
                    $command->service,
                    $command->company,
                    $command->credential
                );
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\RecommendationException('Error while trying to upsert a recommendation', 404, $e);
        }

        return $recommendation;
    }
}
