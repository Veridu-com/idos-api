<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Extension;

/**
 * Trait to generate a UnixTimestamp on Models.
 */
trait CreatedUnixTimestamp {
    /**
     * Get the created_at in UnixTimestamp.
     *
     * @return int
     */
    public function getCreatedAttribute() {
        if (! isset($this->attributes['created_at']))
            return 0;

        return strtotime($this->attributes['created_at']);
    }
}
