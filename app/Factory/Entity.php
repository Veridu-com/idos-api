<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Factory;

use App\Entity\EntityInterface;
use Jenssegers\Optimus\Optimus;

/**
 * Entity Factory Implementation.
 */
class Entity extends AbstractFactory {
    /**
     * Optimus variable.
     *
     * @var \Jessengers\Optimus\Optimus
     */
    private $optimus;

    public function __construct(Optimus $optimus) {
        $this->optimus = $optimus;
    }
    /**
     * {@inheritdoc}
     */
    protected function getNamespace() : string {
        return '\\App\\Entity\\';
    }

    /**
     * Creates new entity instances.
     *
     * @param string $name
     * @param array  $attributes
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function create(string $name, array $attributes = []) : EntityInterface {
        $class = $this->getClassName($name);

        if (class_exists($class)) {
            return new $class($attributes, $this->optimus);
        }

        throw new \RuntimeException(sprintf('Class (%s) not found.', $class));
    }
}
