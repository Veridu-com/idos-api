<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Profile;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;
use Respect\Validation\Validator;

/**
 * Attribute Validation Rules.
 */
class Attribute implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertEntity,
        Traits\AssertName,
        Traits\AssertValue,
        Traits\AssertType;

    /**
     * Asserts a valid attribute array.
     *
     * @param mixed  $values
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAttributeArray($values, string $name = 'attributes') : void {
        Validator::arrayType()
            ->setName($name)
            ->assert($values);

        foreach ($values as $index => $value) {
            Validator::key('user_id')
                ->setName(sprintf('%s[%d].user_id', $name, $index))
                ->assert($value);
            Validator::key('name')
                ->setName(sprintf('%s[%d].name', $name, $index))
                ->assert($value);
            Validator::key('value')
                ->setName(sprintf('%s[%d].value', $name, $index))
                ->assert($value);

            $this->assertId($value['user_id'], sprintf('%s[%d].user_id', $name, $index));
            $this->assertLongName($value['name'], sprintf('%s[%d].name', $name, $index));
        }
    }
}
