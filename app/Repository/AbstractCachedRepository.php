<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use Apix\Cache\PsrCache\TaggablePool;
use App\Entity\EntityInterface;
use Illuminate\Support\Collection;

/**
 * Abstract Cache-based Repository.
 */
abstract class AbstractCachedRepository extends AbstractRepository {
    /**
     * Repository Instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    protected $repository;
    /**
     * Cache Instance.
     *
     * @var \Apix\Cache\PsrCache\TaggablePool
     */
    protected $cache;

    /**
     * Cache prefix.
     */
    protected $cachePrefix;

    /**
     * @const CACHE_TTL Cache TTL
     */
    const CACHE_TTL = 3600;

    /**
     * Invalidates cache content for a key.
     *
     * @param string $key
     *
     * @return \Apix\Cache\PsrCache\TaggablePool
     */
    protected function invalidateCacheKey(string $key) : TaggablePool {
        return $this->cache->deleteItem($key);
    }

    /**
     * Invalidates cache content for the given keys.
     *
     * @param array $keys
     *
     * @return \Apix\Cache\PsrCache\TaggablePool
     */
    protected function invalidateCacheKeys(array $keys) : TaggablePool {
        return $this->cache->deleteItems($keys);
    }

    /**
     * Saves an entity on the cache.
     *
     * @return void
     */
    public function cacheEntity(EntityInterface $entity) : void {
        $keys       = $entity->getCacheKeys();
        $tags       = array_merge($keys, [$this->cachePrefix]);
        $serialized = $entity->serialize();

        foreach ($keys as $key) {
            $this->set($key, $entity, $tags);
        }
    }

    /**
     * Saves entities on the cache.
     *
     * @return void
     */
    public function cacheEntities(Collection $entities) : void {
        foreach ($entities as $entity) {
            $this->cacheEntity($entity);
        }
    }

    /**
     * Deletes an entity from the cache.
     *
     * @return void
     */
    public function deleteEntityCache(EntityInterface $entity) : void {
        $keys = $entity->getReferenceCacheKeys();
        $tags = $entity->getCacheKeys();

        $this->invalidateCacheTags($keys);
        $this->invalidateCacheKeys($keys);
    }

    /**
     * Deletes entities from the cache.
     *
     * @return void
     */
    public function deleteEntitiesFromCache(Collection $entities) : void {
        foreach ($entities as $entity) {
            $this->deleteEntityCache($entity);
        }
    }

    /**
     * Clean the cache of the current repository.
     *
     * @return void
     */
    public function purge() : int {
        return $this->invalidateCacheTag($this->cachePrefix);
    }

    /**
     * Removes entity from cache then deletes an entity.
     *
     * @throws \App\Exception\NotFound
     *
     * @return int number of affected rows
     */
    public function delete(int $id, string $key = 'id') : int {
        $this->deleteEntityCache($this->find($id));

        return $this->repository->delete($id);
    }

    /**
     * Removes entity from cache then deletes an entity.
     *
     * @param array $constraints
     *
     * @throws \App\Exception\NotFound
     *
     * @return int number of affected rows
     */
    public function deleteBy(array $constraints) : int {
        $this->deleteEntityCache($this->findOneBy($constraints));

        return $this->repository->deleteBy($constraints);
    }

    /**
     * Invalidates cache content for a tag.
     *
     * @param string $tag
     *
     * @return void
     */
    protected function invalidateCacheTag(string $tag) : void {
        return $this->cache->clearByTags([$tag]);
    }

    /**
     * Invalidates cache content for the given tags.
     *
     * @param array $tags
     *
     * @return void
     */
    protected function invalidateCacheTags(array $tags) : void {
        return $this->cache->clearByTags($tags);
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryInterface $repository
     * @param \Apix\Cache\PsrCache\TaggablePool   $cache
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        TaggablePool $cache
    ) {
        $this->repository  = $repository;
        $this->cache       = $cache;
        $this->cachePrefix = $this->entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EntityInterface $entity) : EntityInterface {
        $entity = $this->repository->save($entity);

        $this->deleteEntityCache($entity);
        $this->cacheEntity($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id) : EntityInterface {
        $cacheKey = sprintf('%s.id.%s', $this->cachePrefix, $id);
        $entity   = $this->load($cacheKey);

        if ($entity->isHit()) {
            return $entity->get();
        }

        $entity = $this->repository->findOneBy(['id' => $id]);
        $this->cacheEntity($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $queryParams = []) : Collection {
        $cacheKey  = sprintf('%s/all', $this->cachePrefix);
        $cacheTags = [$this->cachePrefix];

        $entities = $this->load($cacheKey);

        if ($entities) {
            return $entities;
        }

        $entities = $this->repository->getAll();

        foreach ($entities as $entity) {
            if ($entity->id) {
                $entityCacheKey = sprintf('%s/one/%s', $this->cachePrefix, $entity->id);
                $this->set($entityCacheKey, $entity, $cacheTags); // same tags
            }
        }

        $this->set($cacheKey, $entities, $cacheTags);

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug) : EntityInterface {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $constraints, array $queryParams = [], array $columns = ['*']) : Collection {
        $constraintsKey = 'by';
        foreach ($constraints as $key => $value) {
            $constraintsKey .= sprintf('.%s.%s', $key, $value);
        }

        $cacheKey  = sprintf('%s.%s', $this->cachePrefix, $constraintsKey);
        $cacheTags = [$this->cachePrefix];

        $entities = $this->load($cacheKey);

        if ($entities->isHit()) {
            return $entities->get();
        }

        $entities = $this->repository->findBy($constraints);

        // tags the query with all related entities
        foreach ($entities as $entity) {
            foreach ($entity->getCacheKeys() as $key) {
                $cacheTags[] = $key;
            }
        }

        $this->set($cacheKey, $entities, $cacheTags);
        $this->cacheEntities($entities);

        return $entities;
    }

    /**
     * Tries to load "key" from the cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function load(string $key) {
        return $this->cache->getItem($key);
    }

    /**
     * Set a pair key:value into the cache.
     *
     * @param string $key
     * @param $value
     * @param array $tags
     */
    public function set(string $key, $value, array $tags = []) {
        $item = $this->cache->getItem($key);
        $item->setTags($tags);
        $item->set($value);

        return $this->cache->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes) : EntityInterface {
        return $this->repository->create($attributes);
    }
}
