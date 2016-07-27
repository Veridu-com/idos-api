<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Factory;

/**
 * Entity Factory Implementation.
 */
class Entity extends AbstractFactory {
    /**
     * {@inheritdoc}
     */
    protected function getNamespace() {
        return '\\App\\Entity\\';
    }

    /**
     * Creates new entity instances.
     *
     * @param string $name
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function create($name, array $attributes = []) {
        $class = $this->getClassName($name);

        if (class_exists($class))
            return new $class($attributes);

        throw new \RuntimeException(sprintf('Class (%s) not found.', $class));
    }
}
