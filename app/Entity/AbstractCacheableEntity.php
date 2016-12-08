<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;
use Jenssegers\Optimus\Optimus;

/**
 * Abstract Cacheable Entity Implementation.
 */
abstract class AbstractCacheableEntity extends AbstractEntity implements CacheableEntityInterface {
    /**
     * Cache prefix.
     *
     * @var bool
     */
    protected $cachePrefix = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes, Optimus $optimus) {
        $this->cachePrefix = str_replace('App\\Entity\\', '', get_class($this));

        parent::__construct($attributes);
    }
}
