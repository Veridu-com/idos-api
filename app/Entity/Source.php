<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Entity;

/**
 * Sources Entity.
 *
 * @apiEntity schema/source/credentialEntity.json
 */
class Source extends AbstractEntity {
    /**
     * {@inheritdoc}
     */
    protected $visible = ['id', 'user_id','name', 'tags', 'created_at', 'updated_at'];

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
        if (is_object($tags)) {
            foreach (get_object_vars($tags) as $key => $value) {
                if (strpos($key, 'otp_', 0) !== false) {
                    unset($tags->$key);
                }
            }
        }

        return $tags;
    }
}
