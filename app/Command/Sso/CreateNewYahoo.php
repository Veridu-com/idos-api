<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Sso;

/**
 * Sso "Create New Yahoo" Command.
 */
class CreateNewYahoo extends CreateNew {
    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function setParameters(array $parameters) : self {
        parent::setParameters($parameters);

        return $this;
    }
}