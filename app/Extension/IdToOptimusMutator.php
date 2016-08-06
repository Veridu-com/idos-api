<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Extension;

use App\Helper\Utils;

/**
 * Trait to a name mutator.
 * The mutator adds a "slug" property based on the received name.
 */
trait IdToOptimusMutator {
    /**
     * Property Mutator (setter) for $name.
     *
     * @param string $value
     *
     * @return App\Entity\EntityInterface
     */
    public function getIdAttribute(int $value) : self {
        return $this->optimus->encode($value);
    }

}
