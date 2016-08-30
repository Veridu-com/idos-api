<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add company assertion.
 */
trait AssertCompany {
    /**
     * Asserts a valid company entity.
     *
     * @param mixed $company
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCompany($company) {
        Validator::instance('App\\Entity\\Company')
            ->assert($company);
    }
}
