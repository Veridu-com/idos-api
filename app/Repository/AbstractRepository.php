<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Exception\NotFound;
use App\Factory\Entity;
use Illuminate\Support\Collection;

/**
 * Abstract Generic Repository.
 */
abstract class AbstractRepository implements RepositoryInterface {
    /**
     * Entity Factory.
     *
     * @var App\Factory\Entity
     */
    protected $entityFactory;

    /**
     * Class constructor.
     *
     * @param App\Factory\Entity $entityFactory
     *
     * @return void
     */
    public function __construct(Entity $entityFactory) {
        $this->entityFactory = $entityFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes) : EntityInterface {
        $name = str_replace(__NAMESPACE__, '', __CLASS__);

        return $this->entityFactory->create($name, $attributes);
    }

    /**
     * Get the entity name.
     *
     * @return string
     */
    protected function getEntityName() : string {
        if (empty($this->entityName))
            throw new \RuntimeException(sprintf('$entityName property not set in %s', get_class($this)));

        return $this->entityName;
    }

    /**
     * Get the entity class name.
     *
     * @return string
     */
    protected function getEntityClassName() : string {
        return sprintf('\\App\\Entity\\%s', $this->getEntityName());
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $constraints) : EntityInterface {
        $entity = $this->findBy($constraints)->first();

        if (! $entity) {
            throw new NotFound();
        }

        return $entity;
    }

    /**
     * Casts collection items to entities mapped by the repository property $relationships. 
     *
     * @param \Illuminate\Support\Collection $items The items.
     *
     * @return \Illuminate\Support\Collection Collection with items casted to the matched class
     */
    public function castHydrate(Collection $items) : Collection {
        $entity        = $this->create([]);
        $relationships = $entity->relationships;

        foreach ($relationships as $databasePrefix => $entityName) {
            $items = $items->map(function ($item) use ($entityName, $databasePrefix) {
                $item->relations[$databasePrefix] = $this->entityFactory->create($entityName, (array) $item->$databasePrefix());

                return $item;
            });
        }

        return $items;
    }

}
