<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Score\CreateNew;
use App\Command\Score\DeleteAll;
use App\Command\Score\DeleteOne;
use App\Command\Score\UpdateOne;
use App\Command\Score\Upsert;
use App\Entity\Score as ScoreEntity;
use App\Event\Score\Created;
use App\Event\Score\Deleted;
use App\Event\Score\DeletedMulti;
use App\Event\Score\Updated;
use App\Exception\AppException;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\ScoreInterface;
use App\Validator\Score as ScoreValidator;
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
     * @var App\Repository\ScoreInterface
     */
    protected $repository;

    /**
     * Score Validator instance.
     *
     * @var App\Validator\Score
     */
    protected $validator;

    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Score(
                $container
                    ->get('repositoryFactory')
                    ->create('Score'),
                $container
                    ->get('validatorFactory')
                    ->create('Score'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ScoreInterface $repository
     * @param App\Validator\Score           $validator
     * @param \League\Event\Emitter         $emitter
     *
     * @return void
     */
    public function __construct(
        ScoreInterface $repository,
        ScoreValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new score for the given attribute.
     *
     * @param App\Command\Score\CreateNew $command
     *
     * @see App\Repository\DBScore::create
     * @see App\Repository\DBScore::save
     * @see App\Repository\DBScore::hydrateRelations
     *
     * @throws App\Exception\Validate\ScoreException
     * @throws App\Exception\Create\ScoreException
     *
     * @return App\Entity\Score
     */
    public function handleCreateNew(CreateNew $command) : ScoreEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->attribute);
            $this->validator->assertName($command->name);
            $this->validator->assertScore($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
            'user_id'    => $command->user->id,
            'creator'    => $command->service->id,
            'attribute'  => $command->attribute,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = new Created($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\ScoreException('Error while trying to create a score', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param App\Command\Score\UpdateOne $command
     *
     * @see App\Repository\DBScore::findOneByName
     * @see App\Repository\DBScore::save
     * @see App\Repository\DBScore::hydrate
     *
     * @throws App\Exception\Validate\ScoreException
     * @throws App\Exception\Update\ScoreException
     *
     * @return App\Entity\Score
     */
    public function handleUpdateOne(UpdateOne $command) : ScoreEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->attribute);
            $this->validator->assertName($command->name);
            $this->validator->assertScore($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOneByName($command->user->id, $command->service->id, $command->name);

        $entity->attribute = $command->attribute;
        $entity->value     = $command->value;
        $entity->updatedAt = time();

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = new Updated($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\ScoreException('Error while trying to update a score', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param App\Command\Score\Upsert $command
     *
     * @see App\Repository\DBScore::findOneByName
     * @see App\Repository\DBScore::create
     * @see App\Repository\DBScore::save
     * @see App\Repository\DBScore::hydrateRelations
     *
     * @throws App\Exception\NotFound\ScoreException
     * @throws App\Exception\Update\ScoreException
     *
     * @return App\Entity\Score
     */
    public function handleUpsert(Upsert $command) : ScoreEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->attribute);
            $this->validator->assertName($command->name);
            $this->validator->assertScore($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity    = null;
        $inserting = false;
        try {
            $entity = $this->repository->findOneByName($command->user->id, $command->service->id, $command->name);

            $entity->attribute = $command->attribute;
            $entity->value     = $command->value;
            $entity->updatedAt = time();
        } catch (NotFound $e) {
            $inserting = true;

            $entity = $this->repository->create(
                [
                    'user_id'    => $command->user->id,
                    'creator'    => $command->service->id,
                    'attribute'  => $command->attribute,
                    'name'       => $command->name,
                    'value'      => $command->value,
                    'created_at' => time()
                ]
            );
        }

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            if ($inserting) {
                $event = new Created($entity);
            } else {
                $event = new Updated($entity);
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\ScoreException('Error while trying to upsert a score', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a score from a given attribute.
     *
     * @param App\Command\Score\DeleteOne $command
     *
     * @see App\Repository\DBScore::findOneByName
     * @see App\Repository\DBScore::delete
     *
     * @throws App\Exception\Validate\ScoreException
     * @throws App\Exception\NotFound\ScoreException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->name);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOneByName($command->user->id, $command->service->id, $command->name);

        try {
            $affectedRows = $this->repository->delete($entity->id);

            $event = new Deleted($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\ScoreException('No features found for deletion', 404);
        }

        return $affectedRows;
    }

    /**
     * Deletes all score from a given attribute.
     *
     * @param App\Command\Score\DeleteAll $command
     *
     * @see App\Repository\DBScore::findBy
     * @see App\Repository\DBScore::delete
     *
     * @throws App\Exception\Validate\ScoreException
     * @throws App\Exception\AppException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->findBy(
            [
            'user_id' => $command->user->id,
            'creator' => $command->service->id
            ], $command->queryParams
        );

        $affectedRows = 0;

        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event = new DeletedMulti($entities);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting scores');
        }

        return $affectedRows;
    }
}
