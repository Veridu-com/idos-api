<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Listener;

use Apix\Cache\AbstractCache;
use App\Event\AbstractListener;
use League\Event\EventInterface;

class DeleteCacheListener extends AbstractListener {
    public $cache;

    public function __construct(AbstractCache $cache) {
        $this->cache = $cache;
    }

    public function handle(EventInterface $event) {
        if ($event->deleteCacheKey) {
            $this->cache->delete($event->deleteCacheKey);
        }

        if ($event->deleteCacheTag) {
            $this->cache->clean($event->deleteCacheTag);
        }
    }
}
