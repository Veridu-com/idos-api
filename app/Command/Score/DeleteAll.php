<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Score;

use App\Entity\Attribute;
use App\Command\AbstractCommand;

/**
 * Score "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Score's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Score's Attribute.
     *
     * @var App\Entity\Attribute
     */
    public $attribute;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['attribute'])) {
            $this->attribute = $parameters['attribute'];
        }

        return $this;
    }
}
