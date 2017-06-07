<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add trigger name assertion.
 */
trait AssertTriggerName {
    /**
     * Asserts a valid trigger, 1-50 chars long.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertTriggerName($value, string $name = 'trigger') : void {
        Validator::graph()
            ->length(1, 50)
            ->setName($name)
            ->assert($value);
    }
}
