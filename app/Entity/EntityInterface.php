<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Entity Interface.
 */
interface EntityInterface {
    /**
     * Initialize entity attributes from a plain array.
     *
     * @param array $attributes
     *
     * @return \App\Entity\EntityInterface
     */
    public function hydrate(array $attributes = []) : self;

    /**
     * Convert the entity instance to an array that can be safely exposed.
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * Gets the raw value of an attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getRawAttribute(string $key);

    /**
     * Serialize the entity instance to an array.
     *
     * @return array
     */
    public function serialize() : array;

    /**
     * Determine if the entity exists on the repository.
     *
     * @return bool
     */
    public function exists() : bool;

    /**
     * Determine if the entity have been modified.
     *
     * @return bool
     */
    public function isDirty() : bool;
}
