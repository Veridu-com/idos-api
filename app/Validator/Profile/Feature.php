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
 * Feature Validation Rules.
 */
class Feature implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertEntity,
        Traits\AssertName,
        Traits\AssertFlag,
        Traits\AssertValue,
        Traits\AssertType;

    /**
     * Asserts a valid feature array.
     *
     * @param mixed  $values
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertFeatureArray($values, string $name = 'features') : void {
        Validator::arrayType()
            ->setName($name)
            ->assert($values);

        foreach ($values as $index => $value) {
            Validator::key('name')
                ->setName(sprintf('%s[%d].name', $name, $index))
                ->assert($value);
            Validator::key('value')
                ->setName(sprintf('%s[%d].value', $name, $index))
                ->assert($value);
            Validator::key('type')
                ->setName(sprintf('%s[%d].type', $name, $index))
                ->assert($value);

            $this->assertLongName($value['name'], sprintf('%s[%d].name', $name, $index));
            $this->assertName($value['type'], sprintf('%s[%d].type', $name, $index));
            if (isset($value['source_id'])) {
                $this->assertId($value['source_id'], sprintf('%s[%d].source_id', $name, $index));
            }
        }
    }
}
