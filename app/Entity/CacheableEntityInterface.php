<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Cacheable Entity Interface.
 */
interface CacheableEntityInterface {
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
}
