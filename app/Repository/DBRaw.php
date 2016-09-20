<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\Raw;
use App\Entity\Source;
use App\Exception\NotFound;
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
    protected $entityName = 'Raw';

    public function findByUserId(int $userId, array $queryParams = []) : Collection {
        $rawFilters    = [];
        $sourceFilters = [];
        foreach ($queryParams as $param => $value) {
            if (substr_compare($param, 'source:', 0, 7) === 0) {
                $param                 = substr($param, 8);
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

        $sourceRepository = $this->repositoryFactory->create('Source');
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

                    $entities->push($entity);

                    if (isset($rawFilters['filter:limit']) && $entities->count() >= (int) $rawFilters['filter:limit']) {
                        break 2;
                    }
                } catch (NotFound $e) {
                }
            }
        }

        if(isset($rawFilters['filter:order'])) {
            $sort = 'ASC';

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
                        } else {
                            return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
                        }
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
    public function deleteBySource(Source $source) : int {
        $this->selectDatabase($source->name);

        $collections  = $this->listCollections();
        $affectedRows = 0;

        foreach($collections as $collection) {
            $affectedRows += $this->deleteOneBySourceAndCollection($source, $collection->getName());
        }

        return $affectedRows;
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

        unset($entity->source);

        return parent::save($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(Source $source, string $collection) : Raw {
        $this->selectDatabase($source->name);
        $this->selectCollection($collection);

        $entity = $this->find($source->id);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySourceAndCollection(Source $source, string $collection) : Raw {
        $this->selectDatabase($source->name);
        $this->selectCollection($collection);

        return $this->find($source->id);
    }

    /**
     * {@inheritdoc}
     */
    public function updateOneBySourceAndCollection(Source $source, string $collection, string $data) : Raw {
        $entity = $this->findOneBySourceAndCollection($source, $collection);

        $entity->source = $source;
        $entity->data   = $data;

        return $this->save($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneBySourceAndCollection(Source $source, string $collection) : int {
        $this->selectDatabase($source->name);
        $this->selectCollection($collection);

        $affectedRows = (int) $this->delete($source->id);

        return $affectedRows;
    }
}
