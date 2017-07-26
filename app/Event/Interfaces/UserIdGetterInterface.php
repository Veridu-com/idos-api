<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Interfaces;

/**
 * User Id getter interface.
 */
interface UserIdGetterInterface {
    /**
     * Gets the user id.
     */
    public function getUserId() : int;
}
