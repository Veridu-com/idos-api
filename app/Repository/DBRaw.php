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

    /**
     * {@inheritdoc}
     */
    public function getAllBySourceAndNames(Source $source, array $names) : Collection {
        $this->selectDatabase($source->name);

        $collections = $this->listCollections();
        $entities    = new Collection();

        foreach($collections as $collection) {
            if (! empty($names) && ! in_array($collection->getName(), $names)) {
                continue;
            }

            $this->selectCollection($collection->getName());

            $entity       = $this->find($source->id);
            $entity->name = $collection->getName();

            $entities->push($entity);
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
            $affectedRows++;
        }

        if (! $this->dropDatabase()->ok) {
            throw new AppException('Could not drop MongoDB database');
        }

        return $affectedRows;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity) : EntityInterface {
        $this->selectDatabase($entity->source->name);
        $this->selectCollection($entity->name);

        if (! $entity->id) {
            $entity->id = $entity->source->id;
        }

        unset($entity->source);

        return parent::save($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySourceAndName(Source $source, string $name) : Raw {
        $this->selectDatabase($source->name);
        $this->selectCollection($name);

        return $this->find($source->id);
    }

    /**
     * {@inheritdoc}
     */
    public function updateOneBySourceAndName(Source $source, string $name, string $data) : Raw {
        $entity = $this->findOneBySourceAndName($source, $name);

        $entity->source = $source;
        $entity->data   = $data;

        return $this->save($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneBySourceAndName(Source $source, string $name) : int {
        $this->selectDatabase($source->name);
        $this->selectCollection($name);

        $affectedRows = (int) $this->dropCollection()->ok;

        return $affectedRows;
    }
}
