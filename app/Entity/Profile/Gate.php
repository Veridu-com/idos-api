<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;
use App\Helper\Utils;

/**
 * Gates Entity.
 *
 * @apiEntity schema/gate/gateEntity.json
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property string $confidenceLevel
 * @property bool $pass
 * @property int    $user_id
 * @property int    $created_at
 * @property int    $updated_at
 */
class Gate extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = [
        'name',
        'slug',
        'confidence_level',
        'pass',
        'review',
        'creator',
        'created_at',
        'updated_at'
    ];

    /**
     * Sets the name attribute.
     *
     * @param string $value The value
     *
     * @return self
     */
    public function setNameAttribute(string $value) : self {
        $this->attributes['name'] = $value;

        $confidenceLevel = $this->attributes['confidence_level'] ?? '';

        if (empty($value)) {
            $this->attributes['slug'] = Utils::slugify($confidenceLevel);

            return $this;
        }

        if (empty($confidenceLevel)) {
            return $this;
        }

        $this->attributes['slug'] = Utils::slugify(sprintf('%s-%s', $confidenceLevel, $value));

        return $this;
    }

    /**
     * Sets the confidence level attribute.
     *
     * @param string $value The value
     *
     * @return self
     */
    public function setConfidenceLevelAttribute($value = null) : self {
        $this->attributes['confidence_level'] = $value;

        $name = $this->attributes['name'] ?? '';

        if (empty($value)) {
            $this->attributes['slug'] = Utils::slugify($name);

            return $this;
        }

        if (empty($name)) {
            return $this;
        }

        $this->attributes['slug'] = Utils::slugify(sprintf('%s-%s', $name, $value));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $obfuscated = ['id', 'creator'];
    /**
     * {@inheritdoc}
     */
    public $relationships = [
        'creator' => 'Service'
    ];
}
