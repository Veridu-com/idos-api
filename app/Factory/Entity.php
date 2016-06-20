<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Factory;

/**
 * Entity Factory Implementation.
 */
class Entity extends AbstractFactory {
    /**
     * {@inheritDoc}
     */
    protected function getNamespace() {
        return '\\App\\Entity\\';
    }
}
