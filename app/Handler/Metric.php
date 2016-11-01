<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Metric\ListAll;
use App\Command\Metric\CreateNew;
use App\Exception\Validate;
use App\Exception\Create;
use App\Repository\MetricInterface;
use App\Entity\Metric as MetricEntity;
use App\Validator\Metric as MetricValidator;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;

/**
 * Handles Metrics commands.
 */
class Metric implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var \App\Repository\MetricInterface
     */
    private $repository;
    /**
     * Metric Validator instance.
     *
     * @var \App\Validator\Metric
     */
    private $validator;
    /**
     * Gearman Client instance.
     *
     * @var \GearmanClient
     */
    private $gearmanClient;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Metric(
                $container
                    ->get('repositoryFactory')
                    ->create('Metric'),
                $container
                    ->get('validatorFactory')
                    ->create('Metric'),
                $container
                    ->get('gearmanClient')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\MetricInterface $repository
     * @param \App\Validator\Metric           $validator
     * @param \App\Factory\Event                   $eventFactory
     * @param \League\Event\Emitter                $emitter
     *
     * @return void
     */
    public function __construct(
        MetricInterface $repository,
        MetricValidator $validator,
        \GearmanClient $gearmanClient
    ) {
        $this->repository    = $repository;
        $this->validator     = $validator;
        $this->gearmanClient = $gearmanClient;
    }

    /**
     * Lists all metrics.
     *
     * @param \App\Command\Metric\ListAll $command
     *
     * @see \App\Repository\DBMetric::get
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleListAll(ListAll $command) : Collection {
        $this->validator->assertArray($command->queryParams);

        $entities = $this->repository->get($command->queryParams);

        return $entities;
    }

    /**
     * Creates a new metric.
     *
     * @param \App\Command\Metric\CreateNew $command
     *
     * @see \App\Repository\DBMetric::create
     * @see \App\Repository\DBMetric::save
     *
     * @return \App\Entity\Metric
     */
    public function handleCreateNew(CreateNew $command) : bool {
        try {
            $this->validator->assertEvent($command->event);
        } catch (ValidationException $e) {
            throw new Validate\MetricException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $eventClass = explode('\\', get_class($command->event));
        $eventType = array_pop($eventClass);
        $eventNamespace = implode('\\', $eventClass);
        $endpointName = strtolower(array_pop($eventClass));
        $endpointType = array_pop($eventClass);

        $payload = [];

        if ($eventNamespace == 'App\\Event\\Company') {
            $payload['endpoint'] = 'company';
        } else if($endpointType == 'Company') {
            $payload['endpoint']    = 'company:' . $endpointName;
        } else if ($endpointType == 'Profile') {
            $payload['endpoint']    = 'profile:' . $endpointName;
        }

        $payload['id'] = $command->event->$endpointName->id;
        $payload['actor_id'] = $command->event->actor->id;
        $payload['created_at'] = time();
        $payload['action']     = strtolower($eventType);

        $this->gearmanClient->doBackground(
            sprintf('idos-metrics-%s', str_replace('.', '', __VERSION__)),
            json_encode($payload)
        );

        return $this->gearmanClient->returnCode() == \GEARMAN_SUCCESS;
    }
}
