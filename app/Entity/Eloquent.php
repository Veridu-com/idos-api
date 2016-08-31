<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

use Carbon\Carbon;
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
     * Entity original attribute values.
     *
     * @var array
     */
    protected $original = [];
    /**
     * The attributes that should be visible in public arrays.
     *
     * @var array
     */
    protected $visible = [];
    /**
     * The attributes that should be hidden in public arrays.
     *
     * @var array
     */
    protected $hidden = [];
    /**
     * The accessors to append to the entity.
     *
     * @var array
     */
    protected $appends = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var arrya
     */
    protected $fillable = [];
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['*'];
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
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
     * Cache prefix.
     *
     * @var bool
     */
    protected $cachePrefix = null;

    /**
     * Created At.
     *
     * @const CREATED_AT
     */
    const CREATED_AT = 'created_at';
    /**
     * Updated At.
     *
     * @const UPDATED_AT
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Class constructor.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = []) {
        $this->cachePrefix = str_replace('App\\Entity\\', '', get_class($this));

        $this->syncOriginal();

        $this->fill($attributes);
    }

    /**
     * Fill the entity with an array of attributes.
     *
     * @param array $attributes
     *
     * @throws \RuntimeException
     *
     * @return EntityInterface
     */
    public function fill(array $attributes) : EntityInterface {
        $totallyGuarded = $this->totallyGuarded();

        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            // The developers may choose to place some attributes in the "fillable"
            // array, which means only those attributes may be set through mass
            // assignment to the model, and all others will just be ignored.
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded) {
                throw new \RuntimeException(sprintf('Cannot mass assign "%s"!', $key));
            }
        }

        return $this;
    }

    /**
     * Get the fillable attributes of a given array.
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function fillableFromArray(array $attributes) {
        if (count($this->getFillable()) > 0) {
            return array_intersect_key($attributes, array_flip($this->getFillable()));
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $attributes = []) : EntityInterface {
        foreach ($attributes as $key => $value) {
            $this->setRawAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Get Entity's hidden attributes.
     *
     * @return array
     */
    public function getHidden() : array {
        return $this->hidden;
    }

    /**
     * Set Entity's hidden attributes.
     *
     * @param array $hidden
     *
     * @return EntityInterface
     */
    public function setHidden(array $hidden) : EntityInterface {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Add hidden attributes to the Entity.
     *
     * @param array|string|null $attributes
     *
     * @return void
     */
    public function addHidden($attributes = null) {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $this->hidden = array_merge($this->hidden, $attributes);
    }

    /**
     * Make the given, typically hidden, attributes visible.
     *
     * @param array|string $attributes
     *
     * @return EntityInterface
     */
    public function makeVisible($attributes) : EntityInterface {
        $this->hidden = array_diff($this->hidden, (array) $attributes);

        if (! empty($this->visible)) {
            $this->addVisible($attributes);
        }

        return $this;
    }

    /**
     * Get Entity's visible attributes.
     *
     * @return array
     */
    public function getVisible() : array {
        return $this->visible;
    }

    /**
     * Set Entity's visible attributes.
     *
     * @param array $visible
     *
     * @return EntityInterface
     */
    public function setVisible(array $visible) : EntityInterface {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Add visible attributes for the Entity.
     *
     * @param array|string|null $attributes
     *
     * @return void
     */
    public function addVisible($attributes = null) {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $this->visible = array_merge($this->visible, $attributes);
    }

    /**
     * Set the accessors to append to Entity arrays.
     *
     * @param array $appends
     *
     * @return EntityInterface
     */
    public function setAppends(array $appends) : EntityInterface {
        $this->appends = $appends;

        return $this;
    }

    /**
     * Get Entity's fillable attributes.
     *
     * @return array
     */
    public function getFillable() : array {
        return $this->fillable;
    }

    /**
     * Set Entity's fillable attributes.
     *
     * @param array $fillable
     *
     * @return EntityInterface
     */
    public function fillable(array $fillable) : EntityInterface {
        $this->fillable = $fillable;

        return $this;
    }

    /**
     * Get Entity's guarded attributes.
     *
     * @return array
     */
    public function getGuarded() {
        return $this->guarded;
    }

    /**
     * Set Entity's guarded attributes.
     *
     * @param array $guarded
     *
     * @return EntityInterface
     */
    public function guard(array $guarded) : EntityInterface {
        $this->guarded = $guarded;

        return $this;
    }

    /**
     * Disable all mass assignable restrictions.
     *
     * @param bool $state
     *
     * @return void
     */
    public static function unguard($state = true) {

        static::$unguarded = $state;
    }

    /**
     * Enable the mass assignment restrictions.
     *
     * @return void
     */
    public static function reguard() {

        static::$unguarded = false;
    }

    /**
     * Determine if current state is "unguarded".
     *
     * @return bool
     */
    public static function isUnguarded() {

        return static::$unguarded;
    }

    /**
     * Run the given callable while being unguarded.
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public static function unguarded(callable $callback) {

        if (static::$unguarded) {
            return $callback();
        }

        static::unguard();

        try {
            return $callback();
        } finally {
            static::reguard();
        }
    }

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isFillable($key) {

        if (static::$unguarded) {
            return true;
        }

        // If the key is in the "fillable" array, we can of course assume that it's
        // a fillable attribute. Otherwise, we will check the guarded array when
        // we need to determine if the attribute is black-listed on the model.
        if (in_array($key, $this->getFillable())) {
            return true;
        }

        if ($this->isGuarded($key)) {
            return false;
        }

        return empty($this->getFillable()) && ! Str::startsWith($key, '_');
    }

    /**
     * Determine if the given key is guarded.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isGuarded($key) {

        return in_array($key, $this->getGuarded()) || $this->getGuarded() == ['*'];
    }

    /**
     * Determine if the model is totally guarded.
     *
     * @return bool
     */
    public function totallyGuarded() : bool {
        return (count($this->getFillable()) == 0) && ($this->getGuarded() == ['*']);
    }

    /**
     * Convert the Entity instance to an array.
     *
     * @return array
     */
    public function toArray() : array {
        $attributes = $this->getArrayableAttributes();

        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        foreach ($this->getDates() as $key) {
            if (! isset($attributes[$key])) {
                continue;
            }

            $attributes[$key] = $this->serializeDate(
                $this->asDateTime($attributes[$key])
            );
        }

        $mutatedAttributes = $this->getMutatedAttributes();

        // We want to spin through all the mutated attributes for this model and call
        // the mutator for the attribute. We cache off every mutated attributes so
        // we don't have to constantly check on attributes that actually change.
        foreach ($mutatedAttributes as $key) {
            if (! array_key_exists($key, $attributes)) {
                continue;
            }

            $attributes[$key] = $this->mutateAttributeForArray(
                $key, $attributes[$key]
            );
        }

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        foreach ($this->getCasts() as $key => $value) {
            if (! array_key_exists($key, $attributes)
                || in_array($key, $mutatedAttributes)
            ) {
                continue;
            }

            $attributes[$key] = $this->castAttribute(
                $key, $attributes[$key]
            );

            if ($attributes[$key] && ($value === 'date' || $value === 'datetime')) {
                $attributes[$key] = $this->serializeDate($attributes[$key]);
            }
        }

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    /**
     * Get an attribute array of all arrayable attributes.
     *
     * @return array
     */
    protected function getArrayableAttributes() : array {
        return $this->getArrayableItems($this->attributes);
    }

    /**
     * Get all of the appendable values that are arrayable.
     *
     * @return array
     */
    protected function getArrayableAppends() : array {
        if (! count($this->appends)) {
            return [];
        }

        return $this->getArrayableItems(
            array_combine($this->appends, $this->appends)
        );
    }

    /**
     * Get an attribute array of all arrayable values.
     *
     * @param array $values
     *
     * @return array
     */
    protected function getArrayableItems(array $values) {

        if (count($this->getVisible()) > 0) {
            return array_intersect_key($values, array_flip($this->getVisible()));
        }

        return array_diff_key($values, array_flip($this->getHidden()));
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function getAttribute(string $key) {
        if (array_key_exists($key, $this->attributes) || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        throw new \RuntimeException(sprintf('Trying to get value for inexistent "%s" attribute!', $key));
    }

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key) {

        $value = $this->getAttributeFromArray($key);

        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        // If the attribute exists within the cast array, we will convert it to
        // an appropriate native PHP type dependant upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        // If the attribute is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
        if (in_array($key, $this->getDates()) && ! is_null($value)) {
            return $this->asDateTime($value);
        }

        return $value;
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getAttributeFromArray($key) {

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
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
     * Get the value of an attribute using its mutator.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function mutateAttribute($key, $value) {

        return $this->{'get' . Str::studly($key) . 'Attribute'}($value);
    }

    /**
     * Get the value of an attribute using its mutator for array conversion.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function mutateAttributeForArray($key, $value) {

        $value = $this->mutateAttribute($key, $value);

        return $value instanceof Arrayable ? $value->toArray() : $value;
    }

    /**
     * Determine whether an attribute should be cast to a native type.
     *
     * @param string            $key
     * @param array|string|null $types
     *
     * @return bool
     */
    public function hasCast($key, $types = null) {

        if (array_key_exists($key, $this->getCasts())) {
            return $types ? in_array($this->getCastType($key), (array) $types, true) : true;
        }

        return false;
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts() : array {
        return $this->casts;
    }

    /**
     * Determine whether a value is Date / DateTime castable for inbound manipulation.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function isDateCastable($key) {

        return $this->hasCast($key, ['date', 'datetime']);
    }

    /**
     * Determine whether a value is JSON castable for inbound manipulation.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function isJsonCastable($key) {

        return $this->hasCast($key, ['array', 'json', 'object', 'collection']);
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCastType($key) {

        return trim(strtolower($this->getCasts()[$key]));
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function castAttribute($key, $value) {

        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
            case 'datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimeStamp($value);
            default:
                return $value;
        }
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value) {

        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
        if ($this->hasSetMutator($key)) {
            $method = sprintf(
                'set%sAttribute',
                $this->toCamelCase($key)
            );

            return $this->{$method}($value);
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
        elseif ($value && (in_array($key, $this->getDates()) || $this->isDateCastable($key))) {
            $value = $this->fromDateTime($value);
        }

        if ($this->isJsonCastable($key) && ! is_null($value)) {
            $value = $this->asJson($value);
        }

        $this->attributes[$key] = $value;

        return $this;
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
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates() {

        $defaults = [self::CREATED_AT, self::UPDATED_AT];

        return array_merge($this->dates, $defaults);
    }

    /**
     * Convert a DateTime to a storable string.
     *
     * @param \DateTime|int $value
     *
     * @return string
     */
    public function fromDateTime($value) {

        $format = $this->getDateFormat();

        $value = $this->asDateTime($value);

        return $value->format($format);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param mixed $value
     *
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value) {

        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof Carbon) {
            return $value;
        }

         // If the value is already a DateTime instance, we will just skip the rest of
         // these checks since they will be a waste of time, and hinder performance
         // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTimeInterface) {
            return new Carbon(
                $value->format('Y-m-d H:i:s.u'), $value->getTimeZone()
            );
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        return Carbon::createFromFormat($this->getDateFormat(), $value);
    }

    /**
     * Return a timestamp as unix timestamp.
     *
     * @param mixed $value
     *
     * @return int
     */
    protected function asTimeStamp($value) {

        return $this->asDateTime($value)->getTimestamp();
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date) {

        return $date->format($this->getDateFormat());
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    protected function getDateFormat() {

        return $this->dateFormat ?: $this->getConnection()->getQueryGrammar()->getDateFormat();
    }

    /**
     * Set the date format used by the model.
     *
     * @param string $format
     *
     * @return $this
     */
    public function setDateFormat($format) {

        $this->dateFormat = $format;

        return $this;
    }

    /**
     * Encode the given value as JSON.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function asJson($value) {

        return json_encode($value);
    }

    /**
     * Decode the given JSON back into an array or object.
     *
     * @param string $value
     * @param bool   $asObject
     *
     * @return mixed
     */
    public function fromJson($value, $asObject = false) {

        return json_decode($value, ! $asObject);
    }

    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes() {

        return $this->attributes;
    }

    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param array $attributes
     * @param bool  $sync
     *
     * @return $this
     */
    public function setRawAttributes(array $attributes, $sync = false) {

        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        return $this;
    }

    /**
     * Get the model's original attribute values.
     *
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed|array
     */
    public function getOriginal($key = null, $default = null) {

        return Arr::get($this->original, $key, $default);
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal() {

        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Sync a single original attribute with its current value.
     *
     * @param string $attribute
     *
     * @return $this
     */
    public function syncOriginalAttribute($attribute) {

        $this->original[$attribute] = $this->attributes[$attribute];

        return $this;
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param array|string|null $attributes
     *
     * @return bool
     */
    public function isDirty($attributes = null) {

        $dirty = $this->getDirty();

        if (is_null($attributes)) {
            return count($dirty) > 0;
        }

        if (! is_array($attributes)) {
            $attributes = func_get_args();
        }

        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $dirty)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty() {

        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key]
                && ! $this->originalIsNumericallyEquivalent($key)
            ) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Determine if the new and old values for a given key are numerically equivalent.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function originalIsNumericallyEquivalent($key) {

        $current = $this->attributes[$key];

        $original = $this->original[$key];

        return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
    }

    /**
     * Get the mutated attributes for a given instance.
     *
     * @return array
     */
    public function getMutatedAttributes() {

        $class = static::class;

        if (! isset(static::$mutatorCache[$class])) {
            static::cacheMutatedAttributes($class);
        }

        return static::$mutatorCache[$class];
    }

    /**
     * Extract and cache all the mutated attributes of a class.
     *
     * @param string $class
     *
     * @return void
     */
    public static function cacheMutatedAttributes($class) {

        $mutatedAttributes = [];

        // Here we will extract all of the mutated attributes so that we can quickly
        // spin through them after we export models to their array form, which we
        // need to be fast. This'll let us know the attributes that can mutate.
        if (preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods($class)), $matches)) {
            foreach ($matches[1] as $match) {
                if (static::$snakeAttributes) {
                    $match = Str::snake($match);
                }

                $mutatedAttributes[] = lcfirst($match);
            }
        }

        static::$mutatorCache[$class] = $mutatedAttributes;
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

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString() : string {
        return $this->toJson();
    }

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
    private function toSnakeCase(string $string) : string {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $string));
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
    // public function isDirty() : bool {
    //     return $this->dirty;
    // }
}
