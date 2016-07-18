<?php
/*
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';
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
        $words  = explode('_', strtolower($string));
        $return = '';
        foreach ($words as $word)
            $return .= ucfirst(trim($word));

        return $return;
    }

    /**
     * Formats a CamelCase string to snake_case.
     *
     * @param string $string
     *
     * @return string
     */
    private function toSnakeCase($string) {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $string));
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
     * @param mixed  $value
     *
     * @throws \RuntimeException
     *
     * @return App\Entity\EntityInterface
     */
    private function setAttribute($key, $value) {
        $key = $this->toSnakeCase($key);

        if ($this->hasSetMutator($key)) {
            $method = sprintf('set%sAttribute', $this->toCamelCase($key));

            return $this->{$method}($value);
        }

        if ((in_array($key, $this->dates)) && (is_int($value))) {
            $value = date($this->dateFormat, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Get an attribute from the entity.
     *
     * @param string $key
     *
     * @throws \RuntimeException
     *
     * @return mixed|null
     */
    private function getAttribute($key) {
        $key = $this->toSnakeCase($key);

        $value = null;
        if (isset($this->attributes[$key])) {
            $value = $this->attributes[$key];
        }

        if (in_array($key, $this->dates)) {
            $value = strtotime($value);
        }

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
     * {@inheritdoc}
     */
    public function hydrate(array $attributes = []) {
        foreach ($attributes as $key => $value)
            $this->setAttribute($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() {
        if (empty($this->visible)) {
            $attributes = array_keys($this->attributes);
        } else {
            $attributes = $this->visible;
        }

        $return = [];
        foreach ($attributes as $attribute) {
            $return[$attribute] = $this->getAttribute($this->toSnakeCase($attribute));
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize() {
        $attributes = array_keys($this->attributes);
        $return     = [];
        foreach ($attributes as $attribute) {
            $return[$this->toSnakeCase($attribute)] = $this->attributes[$attribute];
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function exists() {
        return $this->exists;
    }

    /**
     * {@inheritdoc}
     */
    public function isDirty() {
        return $this->dirty;
    }

    /**
     * Dynamically retrieve values on the entity.
     *
     * @param string $key
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function __get($key) {
        return $this->getAttribute($this->toSnakeCase($key));
    }

    /**
     * Dynamically set values on the entity.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function __set($key, $value) {
        $this->setAttribute($this->toSnakeCase($key), $value);
        $this->dirty = true;
    }

    /**
     * Determine if an attribute exists on the entity.
     *
     * @param string $key
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function __isset($key) {
        return $this->getAttribute($this->toSnakeCase($key)) !== null;
    }
    /**
     * Unset an attribute on the entity.
     *
     * @param string $key
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function __unset($key) {
        $this->setAttribute($this->toSnakeCase($key), null);
        $this->dirty = true;
    }
}
