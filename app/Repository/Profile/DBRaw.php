<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\EntityInterface;
use App\Entity\Profile\Raw;
use App\Entity\Profile\Source;
use App\Exception\NotFound;
use App\Repository\AbstractNoSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * NoSQL Database-based Raw Data Repository Implementation.
 */
class DBRaw extends AbstractNoSQLDBRepository implements RawInterface {
    /**
     * The collection associated with the repository.
     *
     * @var string
     */
    protected $collectionName = null;
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Raw';

    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'source' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'sources',
            'foreignKey' => 'source_id',
            'key'        => 'id',
            'entity'     => 'Source',
            'nullable'   => false,
            'hydrate'    => [
                'id',
                'name'
            ]
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(string $collection, Source $source) : Raw {
        $this->selectDatabase($source->name);
        $this->selectCollection($collection);

        return $this->find($source->id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySourceAndCollection(string $collection, Source $source) : Raw {
        $this->selectDatabase($source->name);
        $this->selectCollection($collection);

        return $this->find($source->id);
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection {
        $rawFilters    = [];
        $sourceFilters = [];
        foreach ($queryParams as $param => $value) {
            if (substr_compare($param, 'source:', 0, 7) === 0) {
                $param                 = substr($param, 7);
                $sourceFilters[$param] = $value;
            } else {
                $rawFilters[$param] = $value;
            }
        }

        if (isset($rawFilters['filter:order'])) {
            if (substr_compare($rawFilters['filter:order'], 'source:', 0, 7)) {
                $sourceFilters['filter:order'] = $rawFilters['filter:order'];

                if (isset($rawFilters['filter:sort']) && substr_compare($rawFilters['filter:sort'], 'source:', 0, 7)) {
                    $sourceFilters['filter:sort'] = $rawFilters['filter:sort'];
                }
            }
        }

        $sourceRepository = $this->repositoryFactory->create('Profile\\Source');
        $sources          = $sourceRepository->findBy(['user_id' => $userId], $sourceFilters);

        $entities = new Collection();
        foreach ($sources as $source) {
            $this->selectDatabase($source->name);

            $collections = $this->listCollections();
            foreach ($collections as $collection) {
                $collectionName = $collection->getName();

                if (isset($rawFilters['collection'])) {
                    if (! in_array($collectionName, explode(',', $rawFilters['collection']))) {
                        continue;
                    }
                }

                $this->selectCollection($collectionName);

                try {
                    $entity             = $this->find($source->id);
                    $entity->collection = $collection->getName();
                    //@FIXME do this through castHydrateEntity
                    $entity->source = $source->toArray();

                    $entities->push($entity);

                    if (isset($rawFilters['filter:limit']) && $entities->count() >= (int) $rawFilters['filter:limit']) {
                        break 2;
                    }
                } catch (NotFound $e) {
                }
            }
        }

        if (isset($rawFilters['filter:order'])) {
            $sort = 'DESC';

            if (isset($rawFilters['filter:sort']) && in_array($rawFilters['filter:sort'], ['ASC', 'DESC'])) {
                $sort = $rawFilters['filter:sort'];
            }

            switch ($rawFilters['filter:order']) {
                case 'latest':
                    $keys = [];

                    foreach ($entities as $key => $entity) {
                        if ($entity->updated_at) {
                            $keys[] = ['key' => $key, 'timestamp' => $entity->updated_at];
                        } else {
                            $keys[] = ['key' => $key, 'timestamp' => $entity->created_at];
                        }
                    }

                    $comp = function ($a, $b) use ($sort) {
                        if ($a['timestamp'] === $b['timestamp']) {
                            return 0;
                        }

                        if ($sort === 'ASC') {
                            return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
                        }

                        return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
                    };

                    usort($keys, $comp);

                    $orderedEntities = new Collection();
                    foreach ($keys as $value) {
                        $orderedEntities->push($entities[$value['key']]);
                    }

                    $entities = $orderedEntities;
                    break;
            }
        }

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOneBySourceAndCollection(string $collection, Source $source, string $data) : Raw {
        $entity = $this->findOneBySourceAndCollection($collection, $source);

        $entity->source = $source;
        $entity->data   = $data;

        return $this->save($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity) : EntityInterface {
        $this->selectDatabase($entity->source->name);
        $this->selectCollection($entity->collection);

        if (! $entity->id) {
            $entity->id = $entity->source->id;
        }

        $source = $entity->source;
        unset($entity->source);

        $entity = parent::save($entity);
        //@FIXME do this through castHydrateEntity
        $entity->source = $source->toArray();

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneBySourceAndCollection(string $collection, Source $source) : int {
        $this->selectDatabase($source->name);
        $this->selectCollection($collection);

        return (int) $this->delete($source->id);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBySource(Source $source) : int {
        $this->selectDatabase($source->name);

        $collections = $this->listCollections();

        $affectedRows = 0;

        foreach ($collections as $collection) {
            if (substr($collection->getName(), 0, 6) !== 'system') {
                $affectedRows += $this->deleteOneBySourceAndCollection($collection->getName(), $source);
            }
        }

        return $affectedRows;
    }
}
