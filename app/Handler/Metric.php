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
     * Metrics endpoint list.
     *
     * @var array
     */
    private $endpoints = [
        'company' => [
            'metricsTable' => 'company_metrics',
            'actor' => 'identity',
            'tables'    => 'companies'
        ],
        'company:credential' => [
            'metricsTable' => 'credential_metrics',
            'actor' => 'identity',
            'tables'    => 'credentials'
        ],
        'company:hook' => [
            'metricsTable' => 'hook_metrics',
            'actor' => 'identity',
            'tables'    => 'hooks'
        ],
        'company:invitation' => [
            'metricsTable' => 'invitation_metrics',
            'actor' => 'identity',
            'tables'    => 'invitations'
        ],
        'company:member' => [
            'metricsTable' => 'member_metrics',
            'actor' => 'identity',
            'tables'    => 'members'
        ],
        'company:permission' => [
            'metricsTable' => 'permission_metrics',
            'actor' => 'identity',
            'tables'    => 'permissions'
        ],
        'company:setting' => [
            'metricsTable' => 'setting_metrics',
            'actor' => 'identity',
            'tables'    => 'settings'
        ],
        'profile:attribute' => [
            'metricsTable' => 'attribute_metrics',
            'actor' => 'credential',
            'tables'    => 'attributes'
        ],
        'profile:candidate' => [
            'metricsTable' => 'candidate_metrics',
            'actor' => 'credential',
            'tables'    => 'candidates'
        ],
        'profile:feature' => [
            'metricsTable' => 'feature_metrics',
            'actor' => 'credential',
            'tables'    => 'features'
        ],
        'profile:flag' => [
            'metricsTable' => 'flag_metrics',
            'actor' => 'credential',
            'tables'    => 'flags'
        ],
        'profile:gate' => [
            'metricsTable' => 'gate_metrics',
            'actor' => 'credential',
            'tables'    => 'gates'
        ],
        'profile:process' => [
            'metricsTable' => 'process_metrics',
            'actor' => 'credential',
            'tables'    => 'processes'
        ],
        'profile:raw' => [
            'metricsTable' => 'raw_metrics',
            'actor' => 'credential',
            'tables'    => 'raw'
        ],
        'profile:reference' => [
            'metricsTable' => 'reference_metrics',
            'actor' => 'credential',
            'tables'    => 'references'
        ],
        'profile:review' => [
            'metricsTable' => 'review_metrics',
            'actor' => 'identity',
            'tables'    => 'reviews'
        ],
        'profile:score' => [
            'metricsTable' => 'score_metrics',
            'actor' => 'credential',
            'tables'    => 'scores'
        ],
        'profile:source' => [
            'metricsTable' => 'source_metrics',
            'actor' => 'credential',
            'tables'    => 'sources'
        ],
        'profile:tag' => [
            'metricsTable' => 'tag_metrics',
            'actor' => 'identity',
            'tables'    => 'tags'
        ],
        'profile:task' => [
            'table' => 'task_metrics',
            'actor' => 'credential',
            'tables'    => 'tasks'
        ]
    ];

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

        if (! isset($command->queryParams['endpoint']) || ! array_key_exists($command->queryParams['endpoint'], $this->endpoints)) {
            return collect();
        }

        $endpointName = $command->queryParams['endpoint'];
        $endpoint = $this->endpoints[$endpointName];
        $tableName = $endpoint['metricsTable'];

        if (isset($command->queryParams['interval'])) {
            switch ($command->queryParams['interval']) {
                case 'hour':
                    $tableName .= '_hourly';
                    break;

                case 'day':
                    $tableName .= '_daily';
                    break;

                default:
            }
        }

        $this->repository->prepare($endpointName);
        $entities = $this->repository->get();

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

        $payload['actor_id'] = $command->event->actor->id;
        $payload['entity_id'] = $command->event->$endpointName->id;
        $payload['created_at'] = time();
        $payload['action']     = strtolower($eventType);

        $this->gearmanClient->doBackground(
            sprintf('idos-metrics-%s', str_replace('.', '', __VERSION__)),
            json_encode($payload)
        );

        return $this->gearmanClient->returnCode() == \GEARMAN_SUCCESS;
    }
}
