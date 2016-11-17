<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Subscription\CreateNew;
use App\Command\Company\Subscription\DeleteOne;
use App\Entity\Company\Subscription as SubscriptionEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\SubscriptionInterface;
use App\Validator\Company\Subscription as SubscriptionValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Subscription commands.
 */
class Subscription implements HandlerInterface {
    /**
     * Subscription Repository instance.
     *
     * @var \App\Repository\Company\SubscriptionInterface
     */
    private $repository;
    /**
     * Subscription Validator instance.
     *
     * @var \App\Validator\Company\Subscription
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
            return new \App\Handler\Company\Subscription(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Subscription'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Subscription'),
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
     * @param \App\Repository\Company\SubscriptionInterface $repository
     * @param \App\Validator\Company\Subscription           $validator
     * @param \App\Factory\Event                            $eventFactory
     * @param \League\Event\Emitter                         $emitter
     *
     * @return void
     */
    public function __construct(
        SubscriptionInterface $repository,
        SubscriptionValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new Subscription.
     *
     * @param \App\Command\Company\Subscription\CreateNew $command
     *
     * @throws \App\Exception\Validate\Company\SubscriptionException
     * @throws \App\Exception\Create\Company\SubscriptionException
     *
     * @return \App\Entity\Company\Subscription
     */
    public function handleCreateNew(CreateNew $command) : SubscriptionEntity {
        try {
            $this->validator->assertSlug($command->categorySlug);
            $this->validator->assertCredential($command->credential);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\SubscriptionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $subscription = $this->repository->create(
            [
                'identity_id'      => $command->identity->id,
                'category_slug'    => $command->categorySlug,
                'credential_id'    => $command->credential->id,
                'created_at'       => time()
            ]
        );

        try {
            $subscription = $this->repository->save($subscription);
            $event        = $this->eventFactory->create('Company\\Subscription\\Created', $subscription, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\SubscriptionException('Error while trying to create a subscription', 500, $e);
        }

        return $subscription;
    }

    /**
     * Deletes a Subscription.
     *
     * @param \App\Command\Company\Subscription\DeleteOne $command
     *
     * @throws \App\Exception\Validate\Company\SubscriptionException
     * @throws \App\Exception\NotFound\Company\SubscriptionException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->subscriptionId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\SubscriptionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $subscription = $this->repository->find($command->subscriptionId);
        $rowsAffected = $this->repository->delete($command->subscriptionId);

        if (! $rowsAffected) {
            throw new NotFound\Company\SubscriptionException('No subscriptions found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Subscription\\Deleted', $subscription, $command->identity);
        $this->emitter->emit($event);
    }
}
