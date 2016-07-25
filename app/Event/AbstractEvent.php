<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Event;

use League\Event\AbstractEvent as AbstractLeagueEvent;

abstract class AbstractEvent extends AbstractLeagueEvent {
	/**
	 * Will delete assigned cache key if DeleteCache listener is registered to the event
	 *
	 * @see App\Event\DeleteCacheListener
	 */ 
	public $deleteCacheKey;
	/**
	 * Will delete assigned cache tag if DeleteCache listener is registered to the event
	 *
	 * @see App\Event\DeleteCacheListener
	 */ 
	public $deleteCacheTag;
}
