<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Tag\CreateNew;
use App\Command\Profile\Tag\DeleteAll;
use App\Command\Profile\Tag\DeleteOne;
use App\Entity\Profile\Tag as TagEntity;
use App\Exception\Create;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\TagInterface;
use App\Repository\UserInterface;
use App\Validator\Profile\Tag as TagValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Tag commands.
 */
class Tag implements HandlerInterface {
    /**
     * Tag Repository instance.
     *
     * @var App\Repository\Profile\TagInterface
     */
    private $repository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Tag Validator instance.
     *
     * @var App\Validator\Profile\Tag
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Profile\Tag(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Tag'),
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Tag'),
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
     * @param App\Repository\TagInterface        $repository
     * @param App\Repository\CredentialInterface $repository
     * @param App\Validator\Tag                  $validator
     * @param App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter              $emitter
     *
     * @return void
     */
    public function __construct(
        TagInterface $repository,
        UserInterface $userRepository,
        TagValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->validator      = $validator;
        $this->eventFactory   = $eventFactory;
        $this->emitter        = $emitter;
    }

    /**
     * Creates a new Tag.
     *
     * @param App\Command\Profile\Tag\CreateNew $command
     *
     * @throws App\Exception\Validate\TagException
     * @throws App\Exception\Create\TagException
     *
     * @see App\Repository\DBTag::create
     * @see App\Repository\DBTag::save
     *
     * @return App\Entity\Tag
     */
    public function handleCreateNew(CreateNew $command) : TagEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Profile\TagException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $user     = $command->user;
        $identity = $command->identity;

        $tag = $this->repository->create(
            [
                'user_id'     => $user->id,
                'identity_id' => $identity->id,
                'name'        => $command->name,
                'created_at'  => time()
            ]
        );

        try {
            $tag   = $this->repository->save($tag);
            $event = $this->eventFactory->create('Profile\\Tag\\Created', $tag);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\TagException('Error while trying to create a tag', 500, $e);
        }

        return $tag;
    }

    /**
     * Deletes a Tag.
     *
     * @param App\Command\Profile\Tag\DeleteOne $command
     *
     * @throws App\Exception\Validate\TagException
     * @throws App\Exception\NotFound\TagException
     *
     * @see App\Repository\DBTag::findOneByUserIdAndSlug
     * @see App\Repository\DBTag::deleteOneByUserIdAndSlug
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertSlug($command->slug);
        } catch (ValidationException $e) {
            throw new Validate\Profile\TagException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $tag = $this->repository->findOneByUserIdAndSlug($command->user->id, $command->slug);

        $rowsAffected = $this->repository->deleteOneByUserIdAndSlug($command->user->id, $command->slug);

        if (! $rowsAffected) {
            throw new NotFound\Profile\TagException('No tags found for deletion', 404);
        }

        $event = $this->eventFactory->create('Profile\\Tag\\Deleted', $tag);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all tags ($command->companyId).
     *
     * @param App\Command\Profile\Tag\DeleteAll $command
     *
     * @see App\Repository\DBTag::getAllByUserId
     * @see App\Repository\DBTag::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $tags = $this->repository->getAllByUserId($command->user->id);

        $rowsAffected = $this->repository->deleteByUserId($command->user->id);

        $event = $this->eventFactory->create('Profile\\Tag\\DeletedMulti', $tags);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
