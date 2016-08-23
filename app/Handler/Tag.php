<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\Tag\CreateNew;
use App\Command\Tag\DeleteAll;
use App\Command\Tag\DeleteOne;
use App\Entity\Tag as TagEntity;
use App\Repository\TagInterface;
use App\Repository\UserInterface;
use App\Validator\Tag as TagValidator;
use Illuminate\Database\QueryException;
use Interop\Container\ContainerInterface;

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
                    ->create('Tag')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\TagInterface        $repository
     * @param App\Repository\CredentialInterface $repository
     * @param App\Validator\Tag                  $validator
     *
     * @return void
     */
    public function __construct(
        TagInterface $repository,
        UserInterface $userRepository,
        TagValidator $validator
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->validator      = $validator;
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

        $user = $command->user;

        $tag = $this->repository->create([
            'user_id'    => $user->id,
            'name'       => $command->name,
            'created_at' => time()
        ]);

        try {
            $tag = $this->repository->save($tag);
        } catch (QueryException $e) {
            $tag = $this->repository->findOneByUserIdAndName($user->id, $command->name);
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
        $this->validator->assertName($command->name);

        $user = $command->user;

        return $this->repository->deleteOneByUserIdAndName($user->id, $command->name);
    }

    /**
     * Deletes all tags ($command->companyId).
     *
     * @param App\Command\Tag\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $user = $command->user;

        return $this->repository->deleteByUserId($user->id);
    }

}
