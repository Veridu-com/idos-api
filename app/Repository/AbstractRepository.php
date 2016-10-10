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
use App\Factory\Repository;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;

/**
 * Abstract Generic Repository.
 */
abstract class AbstractRepository implements RepositoryInterface {
    /**
     * Entity Factory.
     *
     * @var \App\Factory\Entity
     */
    protected $entityFactory;

    /**
     * Repository Factory.
     *
     * @var \App\Factory\Repository
     */
    protected $repositoryFactory;

    /**
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    protected $optimus;

    /**
     * Entity relationships.
     *
     * @var array
     */
    protected $relationships = [];

    /**
     * Entity filterable columns.
     *
     * @var array
     */
    protected $filterableKeys = [];

    /**
     * Class constructor.
     *
     * @param \App\Factory\Entity         $entityFactory
     * @param \App\Factory\Repository     $repositoryFactory
     * @param \Jenssegers\Optimus\Optimus $optimus
     *
     * @return void
     */
    public function __construct(Entity $entityFactory, Repository $repositoryFactory, Optimus $optimus) {
        $this->entityFactory     = $entityFactory;
        $this->repositoryFactory = $repositoryFactory;
        $this->optimus           = $optimus;
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
        if (empty($this->entityName)) {
            throw new \RuntimeException(sprintf('$entityName property not set in %s', get_class($this)));
        }

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
    public function findOneBy(array $constraints, array $queryParams = [], array $columns = ['*']) : EntityInterface {
        $entity = $this->findBy($constraints, $queryParams, $columns)->first();

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
        return $items->map(
            function ($item) {
                return $this->castHydrateEntity($item);
            }
        );
    }

    /**
     * Casts entity mapped by the repository property $relationships.
     *
     * @param \App\Entity\EntityInterface $entity The entity.
     *
     * @return \App\Entity\EntityInterface
     */
    public function castHydrateEntity(EntityInterface &$entity) : EntityInterface {
        $relationships = $entity->relationships;

        if (! $relationships) {
            return $entity;
        }

        foreach ($relationships as $databasePrefix => $entityName) {
            if (! isset($this->relationships[$databasePrefix])) {
                continue;
            }

            $relationProperties = $this->relationships[$databasePrefix];
            //@FIXME: make this method work for all relationship types
            $foreignKey = $relationProperties['foreignKey'];
            if (! $relationProperties['hydrate']) {
                $entity->$databasePrefix = $entity->$foreignKey;
                continue;
            }

            if ($relationProperties['nullable'] && $entity->$foreignKey === null) {
                $entity->relations[$databasePrefix] = null;
                continue;
            }

            $entity->relations[$databasePrefix] = $this->entityFactory->create(
                $entityName,
                (array) $entity->$databasePrefix()
            );
        }

        return $entity;
    }
}
