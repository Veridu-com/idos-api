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
     * @param \GearmanClient                  $gearmanClient
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

        $endpoints = [
            'profile:source',
            'profile:gate'
        ];

        if (! in_array($command->queryParams['endpoint'], $endpoints)) {
            return collect();
        }

        $metricType = null;
        if (isset($command->queryParams['interval'])) {
            switch ($command->queryParams['interval']) {
                case 'hourly':
                case 'daily':
                    $metricType = $command->queryParams['interval'];
                    break;

                default:
            }
        }

        $from = isset($command->queryParams['from']) ? (int) $command->queryParams['from'] : null;
        $to = isset($command->queryParams['to']) ? (int) $command->queryParams['to'] : null;

        $this->repository->prepare($metricType);
        $entities = $this->repository->getByDateInterval($from, $to);

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
        } catch (ValidateException $e) {
            throw new Validate\MetricException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $eventClass = explode('\\', substr(get_class($command->event), strlen('App\\Event\\')));
        $action = strtolower(array_pop($eventClass));
        $endpoint = strtolower($eventClass[0]);
        if (count($eventClass) > 1) {
            $endpoint .= ':' . strtolower($eventClass[1]);
        }

        $entityName = strpos($endpoint, ':') === false ? $endpoint : substr($endpoint, strpos($endpoint, ':') + 1);
        $payload = [
            'endpoint' => $endpoint,
            'action' => $action,
            'created'  => time()
        ];

        switch ($endpoint) {
            case 'profile:source':
            case 'profile:gate':
                $credential = $command->event->credential->toArray();
                $credential['id'] = $command->event->credential->id;

                $payload['credential'] = $credential;
                $payload[$entityName] = $command->event->$entityName->toArray();
                break;

            default:
        }

        $this->gearmanClient->doBackground(
            sprintf('idos-metrics-%s', str_replace('.', '', __VERSION__)),
            json_encode($payload)
        );

        return $this->gearmanClient->returnCode() == \GEARMAN_SUCCESS;
    }
}
