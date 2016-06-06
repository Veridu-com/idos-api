<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Factory;

/**
 * Validator Factory Implementation.
 */
class Validator extends AbstractFactory {
    /**
     * {@inheritDoc}
     */
    protected function getNamespace() {
        return '\\App\\Validator\\';
    }
}
