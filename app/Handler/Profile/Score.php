<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Score\CreateNew;
use App\Command\Profile\Score\DeleteAll;
use App\Command\Profile\Score\DeleteOne;
use App\Command\Profile\Score\UpdateOne;
use App\Command\Profile\Score\UpsertOne;
use App\Entity\Profile\Score as ScoreEntity;
use App\Exception\AppException;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\Profile\Score as ScoreValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Score commands.
 */
class Score implements HandlerInterface {
    /**
     * Score Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Score Validator instance.
     *
     * @var \App\Validator\Profile\Score
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
            return new \App\Handler\Profile\Score(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Score'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Score'),
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
     * @param \App\Repository\RepositoryInterface $repository
     * @param \App\Validator\Profile\Score        $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        ScoreValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new score for the given attribute.
     *
     * @param \App\Command\Profile\Score\CreateNew $command
     *
     * @see \App\Repository\DBScore::create
     * @see \App\Repository\DBScore::save
     * @see \App\Repository\DBScore::hydrateRelations
     *
     * @throws \App\Exception\Validate\Profile\ScoreException
     * @throws \App\Exception\Create\Profile\ScoreException
     *
     * @return \App\Entity\Profile\Score
     */
    public function handleCreateNew(CreateNew $command) : ScoreEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertName($command->attribute, 'attribute');
            $this->validator->assertName($command->name, 'name');
            $this->validator->assertScore($command->value, 'value');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ScoreException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $entity = $this->repository->create(
            [
            'user_id'    => $command->user->id,
            'creator'    => $command->handler->id,
            'attribute'  => $command->attribute,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\Score\Created', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\ScoreException('Error while trying to create a score', 500, $exception);
        }

        return $entity;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param \App\Command\Profile\Score\UpdateOne $command
     *
     * @see \App\Repository\DBScore::findOne
     * @see \App\Repository\DBScore::save
     * @see \App\Repository\DBScore::hydrate
     *
     * @throws \App\Exception\Validate\Profile\ScoreException
     * @throws \App\Exception\Update\Profile\ScoreException
     *
     * @return \App\Entity\Profile\Score
     */
    public function handleUpdateOne(UpdateOne $command) : ScoreEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertName($command->name, 'name');
            $this->validator->assertScore($command->value, 'value');
            $this->validator->assertCredential($command->credential, 'credential');

            // optional parameters
            if ($command->attribute) {
                $this->validator->assertName($command->attribute, 'attribute');
            }
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ScoreException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $entity = $this->repository->findOne($command->name, $command->handler->id, $command->user->id);

        if ($command->attribute) {
            $entity->attribute = $command->attribute;
        }

        $entity->value     = $command->value;
        $entity->updatedAt = time();

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\Score\Updated', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Update\Profile\ScoreException('Error while trying to update a score', 500, $exception);
        }

        return $entity;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param \App\Command\Profile\Score\UpsertOne $command
     *
     * @see \App\Repository\DBScore::findOne
     * @see \App\Repository\DBScore::create
     * @see \App\Repository\DBScore::save
     * @see \App\Repository\DBScore::hydrateRelations
     *
     * @throws \App\Exception\NotFound\Profile\ScoreException
     * @throws \App\Exception\Update\Profile\ScoreException
     *
     * @return \App\Entity\Profile\Score
     */
    public function handleUpsertOne(UpsertOne $command) : ScoreEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertName($command->attribute, 'attribute');
            $this->validator->assertName($command->name, 'name');
            $this->validator->assertScore($command->value, 'value');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ScoreException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        try {
            $score = $this->repository->create(
                [
                    'creator'    => $command->handler->id,
                    'user_id'    => $command->user->id,
                    'attribute'  => $command->attribute,
                    'name'       => $command->name,
                    'value'      => $command->value,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $score = $this->repository->upsert(
                $score,
                [
                    'user_id',
                    'creator',
                    'name'
                ],
                [
                    'attribute'  => $score->getRawAttribute('attribute'),
                    'value'      => $score->getRawAttribute('value'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $score = $this->repository->hydrateRelations($score);

            $eventClass = 'Profile\Score\Created';
            if ($score->updatedAt) {
                $eventClass = 'Profile\Score\Updated';
            }

            $event = $this->eventFactory->create(
                $eventClass,
                $score,
                $command->credential
            );

            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Update\Profile\ScoreException('Error while trying to upsert a score', 500, $exception);
        }

        return $score;
    }

    /**
     * Deletes a score from a given attribute.
     *
     * @param \App\Command\Profile\Score\DeleteOne $command
     *
     * @see \App\Repository\DBScore::findOne
     * @see \App\Repository\DBScore::delete
     *
     * @throws \App\Exception\Validate\Profile\ScoreException
     * @throws \App\Exception\NotFound\Profile\ScoreException
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertName($command->name, 'name');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ScoreException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $entity = $this->repository->findOne($command->name, $command->handler->id, $command->user->id);

        try {
            $affectedRows = $this->repository->delete($entity->id);

            if ($affectedRows) {
                $event = $this->eventFactory->create('Profile\Score\Deleted', $entity, $command->credential);
                $this->emitter->emit($event);
            }
        } catch (\Exception $exception) {
            throw new NotFound\Profile\ScoreException('No features found for deletion', 404);
        }

        return $affectedRows;
    }

    /**
     * Deletes all score from a given attribute.
     *
     * @param \App\Command\Profile\Score\DeleteAll $command
     *
     * @see \App\Repository\DBScore::getByUserIdAndHandlerId
     * @see \App\Repository\DBScore::delete
     *
     * @throws \App\Exception\Validate\Profile\ScoreException
     * @throws \App\Exception\AppException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ScoreException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $entities = $this->repository->getByUserIdAndHandlerId(
            $command->handler->id,
            $command->user->id,
            $command->queryParams
        );

        $affectedRows = 0;
        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event = $this->eventFactory->create('Profile\Score\DeletedMulti', $entities, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new AppException('Error while deleting scores');
        }

        return $affectedRows;
    }
}
