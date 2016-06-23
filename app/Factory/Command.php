<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Factory;

/**
 * Command Factory Implementation.
 */
class Command extends AbstractFactory {
    /**
     * {@inheritdoc}
     */
    protected function getNamespace() {
        return '\\App\\Command\\';
    }
}
