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
use App\Entity\Score as ScoreEntity;
use App\Event\Score\Created;
use App\Event\Score\Deleted;
use App\Event\Score\DeletedMulti;
use App\Event\Score\Updated;
use App\Exception\Create;
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
     * @return App\Entity\Score
     */
    public function handleCreateNew(CreateNew $command) : ScoreEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertScore($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $score = $this->repository->create(
            [
                'attribute_id' => $command->attribute->id,
                'name'         => $command->name,
                'value'        => $command->value,
                'created_at'   => time()
            ]
        );

        try {
            $score = $this->repository->save($score);
            $this->emitter->emit(new Created($score));
        } catch (\Exception $e) {
            throw new Create\ScoreException('Error while trying to create a score', 500, $e);
        }

        return $score;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param App\Command\Score\UpdateOne $command
     *
     * @return App\Entity\Score
     */
    public function handleUpdateOne(UpdateOne $command) : ScoreEntity {
        try {
            $this->validator->assertScore($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $score        = $this->repository->findOneByUserIdAttributeNameAndName($command->user->id, $command->attribute->name, $command->name);
        $score->value = $command->value;

        try {
            $score = $this->repository->save($score);
            $this->emitter->emit(new Updated($score));
        } catch (\Exception $e) {
            throw new Update\ScoreException('Error while trying to update a score', 500, $e);
        }

        return $score;
    }

    /**
     * Deletes a score from a given attribute.
     *
     * @param App\Command\Score\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertName($command->name);
        } catch (ValidationException $e) {
            throw new Validate\ScoreException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $score = $this->repository->findOneByUserIdAttributeNameAndName($command->user->id, $command->attribute->name, $command->name);

        $affectedRows = $this->repository->deleteOneByAttributeIdAndName($command->attribute->id, $command->name);

        if (! $affectedRows) {
            throw new NotFound\ScoreException('No features found for deletion', 404);
        }

        $this->emitter->emit(new Deleted($score));

        return $affectedRows;
    }

    /**
     * Deletes all score from a given attribute.
     *
     * @param App\Command\Score\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $scores = $this->repository->getAllByUserIdAndAttributeName($command->user->id, $command->attribute->name);

        $affectedRows = $this->repository->deleteByAttributeId($command->attribute->id);
        $this->emitter->emit(new DeletedMulti($scores));

        return $affectedRows;
    }
}
