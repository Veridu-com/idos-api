<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EntityInterface;
use Illuminate\Support\Collection;
use Stash\Pool;

class Cache implements RepositoryInterface {
    private $repository;
    private $pool;
    private $namespace;

    private function getClass($object) : string {
        $className = get_class($object);

        return substr($className, strrpos($className, '\\') + 1);
    }

    private function stringify(array $array) : string {
        $items = [];
        foreach ($array as $item) {
            switch (true) {
                case is_scalar($item):
                    $items[] = $item;
                    break;
                case is_array($item):
                    $items[] = $this->stringify($item);
                    break;
                case is_object($item):
                    if ($item instanceof EntityInterface) {
                        $items[] = $this->stringify($item->serialize());
                        break;
                    }

                    $items[] = $this->getClass($item);
                    break;
            }
        }

        return implode(
            '/',
            array_filter(
                $items,
                function ($item) : bool {
                    return ! empty($item);
                }
            )
        );
    }

    private function getDerivedKey(string $baseKey) : string {
        $item = $this->pool->getItem(sprintf('/keys%s', $baseKey));

        $derivedKey = $item->get();
        if ($item->isMiss()) {
            // let other processes know that this one is rebuilding the derived key
            $item->lock();
            $derivedKey = sprintf('%s/%d', $baseKey, time());

            $item->set($derivedKey);
            $item->expiresAfter(3600);

            // store the derived key
            $this->pool->save($item);
        }

        return $derivedKey;
    }

    private function entityKey(EntityInterface $entity) : string {
        return sprintf('/entity/%s/%d', $this->getClass($entity), $entity->id);
    }

    private function tagEntity(EntityInterface $entity, string $baseKey) : void {
        $item = $this->pool->getItem($this->entityKey($entity));

        $tagList = $item->get();
        if ($item->isMiss()) {
            $tagList = [];
        }

        $item->lock();
        $tagList[$baseKey] = true;

        $item->set($tagList);

        $this->pool->save($item);
    }

    private function invalidateTags(EntityInterface $entity) : void {
        $baseKey = $this->entityKey($entity);
        $item    = $this->pool->getItem($baseKey);

        $tagList = $item->get();
        if ($item->isMiss()) {
            return;
        }

        $item->lock();
        foreach (array_keys($tagList) as $baseKey) {
            $derivedKey = $this->pool->getItem(sprintf('/keys%s', $baseKey));
            if ($derivedKey->isMiss()) {
                continue;
            }

            $derivedKey->lock();

            $derivedKey->set(sprintf('%s/%d', $baseKey, time()));
            $derivedKey->expiresAfter(3600);

            $this->pool->saveDeferred($derivedKey);
        }

        $this->pool->commit();

        $item->set([]);

        $this->pool->save($item);
    }

    public function __construct(RepositoryInterface $repository, Pool $pool) {
        $this->repository = $repository;
        $this->pool       = $pool;
        $this->namespace  = $this->getClass($repository);
    }

    public function __call(string $name, array $arguments) {
        if (in_array($name, ['beginTransaction', 'rollBack', 'commit'])) {
            call_user_func([$this->repository, $name]);

            return;
        }

        if (in_array($name, ['create', 'load'])) {
            return call_user_func_array([$this->repository, $name], $arguments);
        }

        if (preg_match('/^(save|upsert|update.*|delete.*)$/', $name)) {
            $data = call_user_func_array([$this->repository, $name], $arguments);
            if ($data instanceof EntityInterface) {
                $this->invalidateTags($data);
            }

            $this->pool->deleteItem(sprintf('/%s', $this->namespace));

            return $data;
        }

        $baseKey = sprintf(
            '/%s/%s/%s',
            $this->namespace,
            $name,
            $this->stringify($arguments)
        );

        $derivedKey = $this->getDerivedKey($baseKey);

        $item = $this->pool->getItem($derivedKey);

        $data = $item->get();

        if ($item->isMiss()) {
            $item->lock();

            $data = call_user_func_array([$this->repository, $name], $arguments);

            switch (true) {
                case is_scalar($data):
                    // $this->tagScalar($baseKey);
                    // XXX COMPANY USES THIS!
                    // throw new \RuntimeException('is_scalar');
                    break;
                case is_array($data):
                    // $this->tagArray($data, $baseKey);
                    // XXX PROCESS/SETTING/TASK USES THIS!
                    // throw new \RuntimeException('is_array');
                    break;
                case $data instanceof EntityInterface:
                    $this->tagEntity($data, $baseKey);
                    break;
                case $data instanceof Collection:
                    foreach ($data as $entity) {
                        $this->tagEntity($entity, $baseKey);
                    }
                    break;
            }

            $item->set($data);
            $item->expiresAfter(3600);

            $this->pool->save($item);
        }

        return $data;
    }
}
