<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use App\Helper\Vault;
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
     * Entity attribute cast to types.
     *
     * @var array
     */
    protected $cast = [];
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
     * The attributes that should be compressed.
     *
     * @var array
     */
    protected $compressed = [];
    /**
     * The attributes that should be secure.
     *
     * @var array
     */
    protected $secure = [];
    /**
     * The entities that have a relationship with this entity.
     *
     * @var array
     */
    public $relations = [];
    /**
     * The relationships of the entity.
     *
     * @var array
     */
    public $relationships = [];
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
    protected $optimus;
    /**
     * Vault helper.
     *
     * @var \App\Helper\Vault
     */
    protected $vault;

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
     * @return \App\Entity\EntityInterface
     */
    protected function setAttribute(string $key, $value) : EntityInterface {
        $key = $this->toSnakeCase($key);
        if (is_resource($value)) {
            $value = stream_get_contents($value, -1, 0);
        }

        if ($this->hasSetMutator($key)) {
            $method = sprintf('set%sAttribute', $this->toCamelCase($key));

            return $this->{$method}($value);
        }

        // Tests if it is not a encoded json
        // how: a decoded json is never a string.
        if ((in_array($key, $this->json)) && (! is_string($value))) {
            $encoded = json_encode($value);
            if ($encoded === false) {
                throw new \RuntimeException('json_encode failed');
            }

            $value = $encoded;
            unset($encoded);
        }

        if ((in_array($key, $this->dates)) && (is_int($value))) {
            $value = date($this->dateFormat, $value);
        }

        // Tests if it is a compressed field
        if ((in_array($key, $this->compressed)) && ($value)) {
            if (is_array($value)) {
                $encoded = json_encode($value);
                if ($encoded === false) {
                    throw new \RuntimeException('pre-compress failed');
                }

                $value = sprintf('serialized:%s', $encoded);
                unset($encoded);
            }

            if (substr_compare((string) $value, 'compressed:', 0, 11) !== 0) {
                $compressed = gzcompress($value);
                if ($compressed === false) {
                    throw new \RuntimeException('compress failed');
                }

                $value = sprintf(
                    'compressed:%s',
                    $compressed
                );
                unset($compressed);
            }
        }

        // Tests if it is a secure field
        if ((in_array($key, $this->secure)) && ($value)) {
            if (is_array($value)) {
                $encoded = json_encode($value);
                if ($encoded === false) {
                    throw new \RuntimeException('pre-secure failed');
                }

                $value = sprintf('serialized:%s', $encoded);
                unset($encoded);
            }

            if (substr_compare((string) $value, 'secure:', 0, 7) !== 0) {
                $value = sprintf(
                    'secure:%s',
                    $this->vault->lock((string) $value)
                );
            }
        }

        // tries to populate relations array mapped by the "." character
        $split = explode('.', $key);
        if (count($split) > 1) {
            $this->relations[$split[0]][$split[1]] = $value;

            return $this;
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
    protected function getAttribute(string $key) {
        $key = $this->toSnakeCase($key);

        $value = null;
        if (isset($this->attributes[$key])) {
            $value = $this->attributes[$key];
        }

        if ((in_array($key, $this->secure)) && ($value)) {
            if (substr_compare((string) $value, 'secure:', 0, 7) === 0) {
                $unlocked = $this->vault->unlock(substr($value, 7));
                if ($unlocked === null) {
                    throw new \RuntimeException('decrypt failed');
                }

                $value = $unlocked;
                unset($unlocked);
            }

            if (($value) && (substr_compare((string) $value, 'serialized:', 0, 11) === 0)) {
                $decoded = json_decode(substr($value, 11), true);
                if ($decoded === null) {
                    throw new \RuntimeException('post-secure failed');
                }

                $value = $decoded;
                unset($decoded);
            }
        }

        if ((in_array($key, $this->compressed)) && ($value)) {
            if (substr_compare((string) $value, 'compressed:', 0, 11) === 0) {
                $uncompressed = gzuncompress(substr($value, 11));
                if ($uncompressed === false) {
                    throw new \RuntimeException('uncompress failed');
                }

                $value = $uncompressed;
                unset($uncompressed);
            }

            if (($value) && (substr_compare((string) $value, 'serialized:', 0, 11) === 0)) {
                $decoded = json_decode(substr($value, 11), true);
                if ($decoded === null) {
                    throw new \RuntimeException('post-compress failed');
                }

                $value = $decoded;
                unset($decoded);
            }
        }

        if ((in_array($key, $this->dates)) && ($value)) {
            $value = strtotime($value);
        }

        if ((in_array($key, $this->json)) && ($value)) {
            $decoded = json_decode($value);
            if ($decoded === null) {
                throw new \RuntimeException('json_decode failed');
            }

            $value = $decoded;
            unset($decoded);
        }

        if ((isset($this->cast[$key])) && ($value)) {
            switch ($this->cast[$key]) {
                case 'int':
                    $value = (int) $value;
                    break;
                case 'float':
                    $value = (float) $value;
                    break;
                case 'bool':
                    $value = (bool) $value;
                    break;
                case 'string':
                    $value = (string) $value;
                    break;
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
     * @param \App\Helper\Vault           $vault
     *
     * @return void
     */
    public function __construct(array $attributes, Optimus $optimus, Vault $vault) {
        if (! empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $key = $this->toSnakeCase($key);
                if ((in_array($key, $this->json)) || (in_array($key, $this->compressed)) || (in_array($key, $this->secure))) {
                    $this->attributes[$key] = $value;

                    continue;
                }

                $this->setAttribute($key, $value);
            }

            $this->exists = true;
        }

        $this->optimus = $optimus;
        $this->vault   = $vault;
    }

    /**
     * Gets the encoded id.
     *
     * @return int
     */
    public function getEncodedId() : int {
        return $this->optimus->encode($this->attributes['id']);
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
        $attributes = $this->visible;
        if (empty($attributes)) {
            $attributes = array_keys($this->attributes);
        }

        $return = [];
        foreach ($attributes as $attribute) {
            $value = null;
            if ($this->relationships && isset($this->relationships[$attribute], $this->relations[$attribute])) {
                // populating relations
                $relationEntity = $this->$attribute();
                $value          = $this->$attribute()->toArray();

                foreach (array_diff($relationEntity->visible, array_keys($relationEntity->attributes)) as $deleteAttribute) {
                    unset($value[$deleteAttribute]);
                }
            } else {
                // populating own attributes
                $value = $this->getAttribute($attribute);

                // field obfuscation
                if (in_array($attribute, $this->obfuscated) && is_int($value)) {
                    $value = $this->optimus->encode($value);
                }
            }

            $return[$attribute] = $value;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawAttribute(string $key) {
        $snakeKey = $this->toSnakeCase($key);
        if (isset($this->attributes[$snakeKey])) {
            return $this->attributes[$snakeKey];
        }

        return;
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
     * @return mixed
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
