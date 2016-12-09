<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Metric\CreateNew;
use App\Command\Metric\ListAllSystem;
use App\Command\Metric\ListAllUser;
use App\Exception\Validate;
use App\Repository\Metric\SystemInterface;
use App\Repository\Metric\UserInterface;
use App\Validator\Metric as MetricValidator;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Metrics commands.
 */
class Metric implements HandlerInterface {
    /**
     * System Metrics Repository instance.
     *
     * @var \App\Repository\Metric\SystemInterface
     */
    private $systemMetricsRepository;
    /**
     * User Metrics Repository instance.
     *
     * @var \App\Repository\Metric\UserInterface
     */
    private $userMetricsRepository;
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
                    ->create('Metric\System'),
                $container
                    ->get('repositoryFactory')
                    ->create('Metric\User'),
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
     * @param \App\Repository\Metric\SystemInterface $systemMetricsRepository
     * @param \App\Repository\Metric\UserInterface   $userMetricsRepository
     * @param \App\Validator\Metric                  $validator
     * @param \GearmanClient                         $gearmanClient
     *
     * @return void
     */
    public function __construct(
        SystemInterface $systemMetricsRepository,
        UserInterface $userMetricsRepository,
        MetricValidator $validator,
        \GearmanClient $gearmanClient
    ) {
        $this->systemMetricsRepository  = $systemMetricsRepository;
        $this->userMetricsRepository    = $userMetricsRepository;
        $this->validator                = $validator;
        $this->gearmanClient            = $gearmanClient;
    }

    /**
     * Lists all system metrics.
     *
     * @param \App\Command\Metric\ListAllSystem $command
     *
     * @see \App\Repository\Metric\DBSystem::getByDateInterval
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleListAllSystem(ListAllSystem $command) : Collection {
        $this->validator->assertArray($command->queryParams);
        $this->validator->assertIdentity($command->identity);

        $endpoints = [
            'profile:source'
        ];

        if (! in_array($command->queryParams['endpoint'], $endpoints)) {
            return collect();
        }

        $metricType = null;
        if (isset($command->queryParams['interval'])) {
            switch ($command->queryParams['interval']) {
                case 'hourly':
                case 'daily':
                case 'weekly':
                    $metricType = $command->queryParams['interval'];
                    break;

                default:
            }
        }

        $from = isset($command->queryParams['from']) ? (int) $command->queryParams['from'] : null;
        $to   = isset($command->queryParams['to']) ? (int) $command->queryParams['to'] : null;

        $this->systemMetricsRepository->prepare($metricType);
        $entities = $this->systemMetricsRepository->getByIdentityAndDateInterval($command->identity, $from, $to, $command->queryParams);

        return $entities;
    }

    /**
     * Lists all user metrics.
     *
     * @param \App\Command\Metric\ListAllUser $command
     *
     * @see \App\Repository\Metric\DBUser::getByDateInterval
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleListAllUser(ListAllUser $command) : Collection {
        $this->validator->assertArray($command->queryParams);
        $this->validator->assertIdentity($command->identity);

        $from = isset($command->queryParams['from']) ? (int) $command->queryParams['from'] : null;
        $to   = isset($command->queryParams['to']) ? (int) $command->queryParams['to'] : null;

        $entities = $this->userMetricsRepository->getByIdentityAndDateInterval($command->identity, $from, $to, $command->queryParams);

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
     * @return \App\Entity\Metric\System
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

        $eventClass = explode('\\', substr(get_class($command->event), strlen('App\\Event\\')));
        $action     = strtolower(array_pop($eventClass));
        $endpoint   = strtolower($eventClass[0]);
        if (count($eventClass) > 1) {
            $endpoint .= ':' . strtolower($eventClass[1]);
        }

        $entityName = $endpoint;
        if (strpos($endpoint, ':') !== false) {
            $entityName = substr($endpoint, strpos($endpoint, ':') + 1);
        }

        if ($action === 'deletedmulti') {
            $action   = 'deleted';
            $entities = $command->event->{$entityName . 's'};
        } else {
            $entities = [$command->event->$entityName];
        }

        $payload = [
            'endpoint' => $endpoint,
            'action'   => $action,
            'created'  => time()
        ];

        foreach ($entities as $entity) {
            switch ($endpoint) {
                case 'profile:source':
                case 'profile:attribute':
                case 'profile:gate':
                case 'profile:flag':
                    $credential       = $command->event->credential->toArray();
                    $credential['id'] = $command->event->credential->id;

                    $payload['user_id']         = $entity->user_id;
                    $payload['credential']      = $credential;
                    $payload[$entityName]       = $entity->toArray();
                    $payload[$entityName]['id'] = $entity->id;
                    break;

                default:
                    return false;
            }

            $this->gearmanClient->doBackground(
                'metrics',
                json_encode($payload),
                uniqid('metrics-')
            );
        }

        return $this->gearmanClient->returnCode() == \GEARMAN_SUCCESS;
    }
}
