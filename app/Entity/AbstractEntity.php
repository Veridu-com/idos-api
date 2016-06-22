<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Entity;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Abstract Entity Implementation.
 * Heavily inspired on Eloquent's Model.
 *
 * @link https://github.com/illuminate/database/blob/master/Eloquent/Model.php
 */
abstract class AbstractEntity implements EntityInterface, Arrayable {
    /**
     * Entity attribute values.
     *
     * @var array
     */
    protected $attributes = [];
    /**
     * The attributes that should be visible in public arrays.
     *
     * @var array
     */
    protected $visible = [];
    /**
     * Indicates if the entity exists on the repository.
     *
     * @var bool
     */
    protected $exists = false;
    /**
     * Indicates if any entity attribute has been changed.
     *
     * @var bool
     */
    protected $dirty = false;

    /**
     * Formats a snake_case string to CamelCase.
     *
     * @param string $string
     *
     * @return string
     */
    private function toCamelCase($string) {
        $words = explode('_', strtolower($string));
        $return = '';
        foreach ($words as $word)
            $return .= ucfirst(trim($word));
        return $return;
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $key
     *
     * @return bool
     */
    private function hasSetMutator($key) {
        return method_exists(
            $this,
            sprintf(
                'set%sAttribute',
                $this->toCamelCase($key)
            )
        );
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param string $key
     *
     * @return bool
     */
    private function hasGetMutator($key) {
        return method_exists(
            $this,
            sprintf(
                'get%sAttribute',
                $this->toCamelCase($key)
            )
        );
    }

    /**
     * Set a given attribute on the entity.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return App\Entity\EntityInterface
     *
     * @throws \RuntimeException
     */
    private function setAttribute($key, $value) {
        if ($this->hasSetMutator($key)) {
            $method = sprintf('set%sAttribute', $this->toCamelCase($key));
            return $this->{$method}($value);
        }

        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Get an attribute from the entity.
     *
     * @param string $key
     *
     * @return mixed|null
     *
     * @throws \RuntimeException
     */
    private function getAttribute($key) {
        $value = null;
        if (isset($this->attributes[$key]))
            $value = $this->attributes[$key];

        if ($this->hasGetMutator($key)) {
            $method = sprintf('get%sAttribute', $this->toCamelCase($key));
            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * Class constructor.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = []) {
        if (! empty($attributes)) {
            $this
                ->hydrate($attributes)
                ->exists = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $attributes = []) {
        foreach ($attributes as $key => $value)
            $this->setAttribute($key, $value);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray() {
        if (empty($this->visible))
            return $this->serialize();

        $return = [];
        foreach ($this->visible as $attribute)
            $return[$attribute] = $this->getAttribute($attribute);
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize() {
        $return = [];
        foreach (array_keys($this->attributes) as $attribute)
            $return[$attribute] = $this->getAttribute($attribute);
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function exists() {
        return $this->exists;
    }

    /**
     * {@inheritDoc}
     */
    public function isDirty() {
        return $this->dirty;
    }

    /**
     * Dynamically retrieve values on the entity.
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function __get($key) {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set values on the entity.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __set($key, $value) {
        $this->setAttribute($key, $value);
        $this->dirty = true;
    }

    /**
     * Determine if an attribute exists on the entity.
     *
     * @param string $key
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function __isset($key) {
        return ! is_null($this->getAttribute($key));
    }
    /**
     * Unset an attribute on the entity.
     *
     * @param string $key
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __unset($key) {
        $this->setAttribute($key, null);
        $this->dirty = true;
    }
}
