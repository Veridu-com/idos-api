<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Setting;

use App\Event\AbstractEvent;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple settings.
 */
class DeletedMulti extends AbstractEvent {
    /**
     * Event related Settings.
     *
     * @var \Illuminate\Support\Collection
     */
    public $settings;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $settings
     *
     * @return void
     */
    public function __construct(Collection $settings) {
        $this->settings = $settings;
    }
}
