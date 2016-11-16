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
     * Generates a slug for the entity.
     *
     * @param      string  $name             The name
     * @param      string  $confidenceLevel  The confidence level
     * 
     * @return string
     */
    public static function generateSlug(string $name, $confidenceLevel = null) : string {
        if ($confidenceLevel && ! is_string($confidenceLevel)) {
            throw new \RuntimeException("$confidenceLevel parameter must be a string.");
        }

        $confidenceSufix = $confidenceLevel ? sprintf('-%s', $confidenceLevel) : '';
        return Utils::slugify(sprintf('%s%s', $name, $confidenceSufix));
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
