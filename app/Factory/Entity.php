<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Factory;

use App\Entity\EntityInterface;
use App\Helper\Secure;
use Jenssegers\Optimus\Optimus;

/**
 * Entity Factory Implementation.
 */
class Entity extends AbstractFactory {
    /**
     * Optimus variable.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    private $optimus;
    /**
     * Encryption helper.
     *
     * @var \App\Helper\Secure
     */
    private $crypt;

    public function __construct(Optimus $optimus, Secure $crypt) {
        $this->optimus = $optimus;
        $this->crypt   = $crypt;
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
            return new $class($attributes, $this->optimus, $this->crypt);
        }

        throw new \RuntimeException(sprintf('Class (%s) not found.', $class));
    }
}
