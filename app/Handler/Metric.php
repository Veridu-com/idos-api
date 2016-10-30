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
                    ->create('Metric')
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
        MetricValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
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
    public function handleCreateNew(CreateNew $command) : MetricEntity {
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
        $eventTokenType = $eventClass[2];
        $eventEndpoint = $eventClass[count($eventClass) - 2];

        $data = [];
        $this->repository->prepare($eventEndpoint);
        switch ($eventTokenType) {
            case 'Company':
                $data['identity_id'] = $command->event->identity->id;
                break;

            case 'Profile':
                $property = strtolower($eventEndpoint);
                $data['credential_id'] = $command->event->$property->credential_id;
                var_dump($command->event->$property);exit;
                break;

            default:

        }

        var_dump($data);exit;

        $metric = $this->repository->create(
            [
                'name'       => $command->name,
                'value'      => $command->value,
                'created_at' => time()
            ]
        );

        try {
            $metric = $this->repository->save($metric);
        } catch (\Exception $e) {
            throw new Create\MetricException('Error while trying to create a metric', 500, $e);
        }

        return $metric;
    }
}
