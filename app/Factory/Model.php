<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Factory;

/**
 * Model Factory Implementation.
 */
class Model extends AbstractFactory {
    /**
     * {@inheritdoc}
     */
    protected function getNamespace() {
        return '\\App\\Model\\';
    }
}
