<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity\Profile;

use App\Entity\AbstractEntity;

/**
 * Sources Entity.
 *
 * @apiEntity schema/source/sourceEntity.json
 */
class Source extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'name', 'tags', 'created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * {@inheritdoc}
     */
    protected $json = ['tags'];

    /**
     * Gets the tags attribute.
     * Filters all "otp-*" tags.
     *
     * @param null|stdClass $tags The tags
     *
     * @return null|stdClass The modified tags attribute.
     */
    public function getTagsAttribute($tags) {
        $otpAllowed = ['otp_verified', 'otp_voided'];

        if (is_object($tags)) {
            foreach (get_object_vars($tags) as $key => $value) {
                if (strpos($key, 'otp_', 0) !== false) {
                    if (in_array($key, $otpAllowed)) {
                        continue;
                    }

                    unset($tags->$key);
                }
            }
        }

        return $tags;
    }

    /**
     * Sets the tag attribute.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @return self
     */
    public function setTag(string $key, $value) : self {
        $tags                     = $this->tags;
        $tags->$key               = $value;
        $this->attributes['tags'] = json_encode($tags);

        return $this;
    }
}
