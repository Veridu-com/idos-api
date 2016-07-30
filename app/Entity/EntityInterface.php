<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * Entity Interface.
 */
interface EntityInterface {
    /**
     * Initialize the entity from plain array.
     *
     * @param array $attributes
     *
     * @return App\Entity\EntityInterface
     */
    public function hydrate(array $attributes = []) : self;

    /**
     * Gets the entity cache keys.
     *
     * @return array
     */
    public function getCacheKeys() : array;

    /**
     * Gets the entity cache tags
     * Think on it like a pub-sub channels.
     *
     * It is a merge between
     * #1 The entity cache keys - will delete where it was listed already
     * #2 The cache keys that should deleted so those places will miss the cache and get fresh data on the next
     *
     * @return array
     */
    public function getReferenceCacheKeys() : array;

    /**
     * Convert the entity instance to an array that can be safely exposed.
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * Serialize the entity instance to an array.
     *
     * @return array
     */
    public function serialize() : array;

    /**
     * Determine if the entity exists on the repository.
     *
     * @return bool
     */
    public function exists() : bool;

    /**
     * Determine if the entity have been modified.
     *
     * @return bool
     */
    public function isDirty() : bool;
}
