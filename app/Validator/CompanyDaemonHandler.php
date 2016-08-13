<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * CompanyDaemonHandler Validation Rules.
 */
class CompanyDaemonHandler implements ValidatorInterface {
    /**
     * Asserts a valid id.
     *
     * @param mixed $id
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId($id) {
        Validator::digit()
            ->assert($id);
    }

}
