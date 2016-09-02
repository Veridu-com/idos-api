<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Tag\CreateNew;
use App\Command\Tag\DeleteAll;
use App\Command\Tag\DeleteOne;
use App\Entity\Tag as TagEntity;
use App\Event\Tag\Created;
use App\Event\Tag\Deleted;
use App\Event\Tag\DeletedMulti;
use App\Exception\AppException as AppException;
use App\Repository\TagInterface;
use App\Repository\UserInterface;
use App\Validator\Tag as TagValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Tag commands.
 */
class Tag implements HandlerInterface {
    /**
     * Tag Repository instance.
     *
     * @var App\Repository\TagInterface
     */
    protected $repository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $userRepository;
    /**
     * Tag Validator instance.
     *
     * @var App\Validator\Tag
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
            return new \App\Handler\Tag(
                $container
                    ->get('repositoryFactory')
                    ->create('Tag'),
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('Tag'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\TagInterface        $repository
     * @param App\Repository\CredentialInterface $repository
     * @param App\Validator\Tag                  $validator
     * @param \League\Event\Emitter              $emitter
     *
     * @return void
     */
    public function __construct(
        TagInterface $repository,
        UserInterface $userRepository,
        TagValidator $validator,
        Emitter $emitter
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->validator      = $validator;
        $this->emitter        = $emitter;
    }

    /**
     * Creates a new Tag.
     *
     * @param App\Command\Tag\CreateNew $command
     *
     * @return App\Entity\Tag
     */
    public function handleCreateNew(CreateNew $command) : TagEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertSlug($command->slug);

        $user = $command->user;

        $tag = $this->repository->create(
            [
                'user_id'    => $user->id,
                'name'       => $command->name,
                'slug'       => $command->slug,
                'created_at' => time()
            ]
        );

        try {
            $tag   = $this->repository->save($tag);
            $event = new Created($tag);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while trying to create a tag');
        }

        return $tag;
    }

    /**
     * Deletes a Tag.
     *
     * @param App\Command\Tag\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertSlug($command->slug);

        $tag = $this->repository->findOneByUserIdAndSlug($command->user->id, $command->slug);

        $rowsAffected = $this->repository->deleteOneByUserIdAndSlug($command->user->id, $command->slug);

        if ($rowsAffected) {
            $event = new Deleted($tag);
            $this->emitter->emit($event);
        } else {
            throw new NotFound();
        }

        return $rowsAffected;
    }

    /**
     * Deletes all tags ($command->companyId).
     *
     * @param App\Command\Tag\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $tags = $this->repository->getAllByUserId($command->user->id);

        $rowsAffected = $this->repository->deleteByUserId($command->user->id);

        $event = new DeletedMulti($tags);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
