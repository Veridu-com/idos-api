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
            if (strpos($param, ':') === false) {
                $rawFilters[$param] = $value;
            } else {
                $param                 = str_replace('source:', '', $param);
                $sourceFilters[$param] = $value;
            }
        }

        $sourceRepository = $this->repositoryFactory->create('Source');
        $sources          = $sourceRepository->findBy(['user_id' => $userId], $sourceFilters);

        $entities = new Collection();
        foreach ($sources as $source) {
            $this->selectDatabase($source->name);

            $collections = $this->listCollections();
            foreach ($collections as $collection) {
                $this->selectCollection($collection->getName());

                try {
                    $entity             = $this->find($source->id);
                    $entity->collection = $collection->getName();

                    $entities->push($entity);
                } catch (NotFound $e) {
                }
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
