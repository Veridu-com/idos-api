<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Extension;

use App\Helper\Utils;

/**
 * Trait to a name mutator.
 * The mutator adds a "slug" property based on the received name.
 */
trait SlugMutator {
    /**
     * Property Mutator (setter) for $name.
     *
     * @param string $value
     *
     * @return \App\Entity\EntityInterface
     */
    public function setNameAttribute(string $value) : self {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);

        return $this;
    }
}
