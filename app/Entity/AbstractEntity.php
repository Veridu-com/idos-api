<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use Illuminate\Contracts\Support\Arrayable;
use Jenssegers\Optimus\Optimus;

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
     * The attributes that should be mutated to json.
     *
     * @var array
     */
    protected $json = [];
    /**
     * The attributes that should be secure.
     *
     * @var array
     */
    protected $secure = [];
    /**
     * The relations of the entity.
     *
     * @var array
     */
    public $relations = [];
    /**
     * Attributes to obfuscate using Jenssegers\Optimus\Optimus.
     *
     * @var array
     */
    protected $obfuscated = ['id'];
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
     * Optimus.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    protected $optimus = null;

    /**
     * Formats a snake_case string to CamelCase.
     *
     * @param string $string
     *
     * @return string
     */
    private function toCamelCase(string $string) : string {
        $words  = explode('_', strtolower($string));
        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }

        return $return;
    }

    /**
     * Formats a CamelCase string to snake_case.
     *
     * @param string $string
     *
     * @return string
     */
    private function toSnakeCase(string $string) : string {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $string));
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $key
     *
     * @return bool
     */
    private function hasSetMutator(string $key) : bool {
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
    private function hasGetMutator(string $key) : bool {
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
    protected function setAttribute(string $key, $value) : EntityInterface {
        $key = $this->toSnakeCase($key);

        if ($this->hasSetMutator($key)) {
            $method = sprintf('set%sAttribute', $this->toCamelCase($key));

            return $this->{$method}($value);
        }

        // Tests if it is a secure field
        if ((in_array($key, $this->secure))) {
            if (is_resource($value)) {
                $value = stream_get_contents($value, -1, 0);
            }

            if (strpos((string) $value, 'secure:') === false) {
                $this->attributes[$key] = sprintf(
                    'secure:%s',
                    // $this->secure->lock($value)
                    $value
                );
            }
        }

        // Tests if it is not a encoded json
        // how: a decoded json is never a string.
        if ((in_array($key, $this->json)) && (! is_string($value))) {
            $value = json_encode($value);
        }

        if ((in_array($key, $this->dates)) && (is_int($value))) {
            $value = date($this->dateFormat, $value);
        }

        if ((isset($this->secure)) && (in_array($key, $this->secure))) {
            if (is_resource($value)) {
                $value = stream_get_contents($value, -1, 0);
            }

            if (($value) && (substr_compare((string) $value, 'secure:', 0, 7) != 0)) {
                $value = sprintf(
                    'secure:%s',
                    // $this->secure->lock($value)
                    $value
                );
            }
        }

        // tries to populate relations array mapped by the "." character
        $split = explode('.', $key);
        if (count($split) > 1) {
            $this->relations[$split[0]][$split[1]] = $value;
        } else {
            $this->attributes[$key] = $value;
        }

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
    protected function getAttribute(string $key) {
        $key = $this->toSnakeCase($key);

        $value = null;
        if (isset($this->attributes[$key])) {
            $value = $this->attributes[$key];
        }

        if ((isset($this->secure)) && (in_array($key, $this->secure))) {
            if (is_resource($value)) {
                $value = stream_get_contents($value, -1, 0);
            }

            if (($value) && (substr_compare((string) $value, 'secure:', 0, 7) === 0)) {
                $value = substr($value, 7);
                // $value = $this->secure->unlock($value);
            }
        }

        if ((in_array($key, $this->dates)) && ($value !== null)) {
            $value = strtotime($value);
        }

        if ((in_array($key, $this->secure))) {
            if (is_resource($value)) {
                $value = stream_get_contents($value, -1, 0);
            }

            if (strpos((string) $value, 'secure:') === 0) {
                $value = substr($value, 7);
                // $value = $this->secure->unlock($value);
            }
        }

        if ((in_array($key, $this->json)) && ($value !== null)) {
            $value = json_decode($value);
            if ($value === null) {
                $value = [];
            }
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
     * @param array                       $attributes
     * @param \Jenssegers\Optimus\Optimus $optimus
     *
     * @return void
     */
    public function __construct(array $attributes = [], Optimus $optimus) {
        if (! empty($attributes)) {
            $this
                ->hydrate($attributes)
                ->exists = true;
        }

        $this->optimus = $optimus;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $attributes = []) : EntityInterface {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array {
        if (empty($this->visible)) {
            $attributes = array_keys($this->attributes);
        } else {
            $attributes = $this->visible;
        }

        $return = [];
        foreach ($attributes as $attribute) {
            $value = null;
            if ($this->relationships && isset($this->relationships[$attribute]) && isset($this->relations[$attribute])) {
                // populating relations
                $value = $this->$attribute()->toArray();
            } else {
                // populating own attributes
                $value = $this->getAttribute($attribute);

                // field obfuscation
                if (in_array($attribute, $this->obfuscated) && is_int($value)) {
                    $value = $this->optimus->encode($value);
                }
            }

            if ($value !== null) {
                $return[$attribute] = $value;
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize() : array {
        $return = [];
        foreach ($this->attributes as $key => $value) {
            $return[$this->toSnakeCase($key)] = $value;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function exists() : bool {
        return $this->exists;
    }

    /**
     * {@inheritdoc}
     */
    public function isDirty() : bool {
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
    public function __get(string $key) {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically retrieve relations value.
     *
     * @param string $methodName The method name
     * @param array  $args       The arguments
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function __call(string $methodName, array $args) {
        if (! isset($this->relationships[$methodName])) {
            throw new \RuntimeException(
                sprintf(
                    'Relation "%s" is not mapped within the "relationships" property of the class "%s".',
                    $methodName,
                    get_class($this)
                )
            );
        }

        if (! isset($this->relations[$methodName])) {
            throw new \RuntimeException(
                sprintf(
                    'Relation "%s" on "%s" was not populated by the database query.',
                    $methodName,
                    get_class($this)
                )
            );
        }

        return $this->relations[$methodName];
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
    public function __set(string $key, $value) {
        $this->setAttribute($key, $value);
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
    public function __isset(string $key) : bool {
        return $this->getAttribute($key) !== null;
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
    public function __unset(string $key) {
        unset($this->attributes[$key]);
        $this->dirty = true;
    }
}
