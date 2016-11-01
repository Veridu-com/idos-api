<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Widget\CreateNew;
use App\Command\Company\Widget\DeleteOne;
use App\Command\Company\Widget\UpdateOne;
use App\Entity\Company\Widget as WidgetEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\WidgetInterface;
use App\Validator\Company\Widget as WidgetValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Widget commands.
 */
class Widget implements HandlerInterface {
    /**
     * Widget Repository instance.
     *
     * @var \App\Repository\Company\WidgetInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Widget Validator instance.
     *
     * @var \App\Validator\Company\Widget
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
            return new \App\Handler\Company\Widget(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Widget'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Credential'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Widget'),
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
     * @param \App\Repository\Company\WidgetInterface     $repository
     * @param \App\Repository\Company\CredentialInterface $repository
     * @param \App\Validator\Company\Widget               $validator
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
     *
     * @return void
     */
    public function __construct(
        WidgetInterface $repository,
        CredentialInterface $credentialRepository,
        WidgetValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->validator            = $validator;
        $this->eventFactory         = $eventFactory;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a new widget.
     *
     * @param \App\Command\Company\Widget\CreateNew $command
     *
     * @see \App\Repository\DBWidget::findByPubKey
     * @see \App\Repository\DBWidget::create
     * @see \App\Repository\DBWidget::save
     *
     * @throws \App\Exception\Validate\Company\WidgetException
     * @throws \App\Exception\NotFound\Company\WidgetException
     * @throws \App\Exception\Create\Company\WidgetException
     *
     * @return \App\Entity\Company\Widget
     */
    public function handleCreateNew(CreateNew $command) : WidgetEntity {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertIdentity($command->creator);
            $this->validator->assertString($command->label);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\WidgetException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        if ($credential->companyId !== $command->company->id) {
            throw new Validate\Company\WidgetException('Credential does not belong to company.');
        }

        $widget = $this->repository->create(
            [
                'hash'             => md5(sprintf('%s%s%s%s%s', $command->label, microtime(), $credential->id, $command->company->id, $command->creator->id)),
                'label'            => $command->label,
                'type'             => $command->type,
                'config'           => json_encode($command->config),
                'enabled'          => $command->enabled,
                'credential_id'    => $credential->id,
                'company_id'       => $command->company->id,
                'creator_id'       => $command->creator->id,
                'created_at'       => time()
            ]
        );

        try {
            $widget  = $this->repository->save($widget);
            $event   = $this->eventFactory->create('Company\\Widget\\Created', $widget, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\WidgetException('Error while trying to create a widget', 500, $e);
        }

        return $widget;
    }

    /**
     * Updates a widget.
     *
     * @param \App\Command\Company\Widget\UpdateOne $command
     *
     * @see \App\Repository\DBWidget::findByPubKey
     * @see \App\Repository\DBWidget::find
     * @see \App\Repository\DBWidget::save
     *
     * @throws \App\Exception\Validate\Company\WidgetException
     * @throws \App\Exception\NotFound\Company\WidgetException
     * @throws \App\Exception\Update\Company\WidgetException
     *
     * @return \App\Entity\Company\Widget
     */
    public function handleUpdateOne(UpdateOne $command) : WidgetEntity {
        try {
            // $this->validator->assertString($command->label);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\WidgetException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $widget = $this->repository->findByHash($command->hash);

        $widget->label      = $command->label;
        $widget->hash       = $command->hash;
        $widget->type       = $command->type;
        $widget->config     = $command->config;
        $widget->enabled    = $command->enabled;

        try {
            $widget  = $this->repository->save($widget);
            $event   = $this->eventFactory->create('Company\\Widget\\Updated', $widget, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Company\WidgetException('Error while trying to update a widget', 500, $e);
        }

        return $widget;
    }

    /**
     * Deletes a widget.
     *
     * @param \App\Command\Company\Widget\DeleteOne $command
     *
     * @see \App\Repository\DBWidget::findByHash
     * @see \App\Repository\DBWidget::delete
     *
     * @throws \App\Exception\Validate\Company\WidgetException
     * @throws \App\Exception\NotFound\Company\WidgetException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertName($command->hash);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Company\WidgetException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $widget       = $this->repository->findByHash($command->hash);
        $rowsAffected = $this->repository->delete($widget->id);

        if (! $rowsAffected) {
            throw new NotFound\Company\WidgetException('No widgets found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Widget\\Deleted', $widget, $command->identity, $command->identity);
        $this->emitter->emit($event);
    }
}
