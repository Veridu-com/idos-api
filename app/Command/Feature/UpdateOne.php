<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Feature's slug.
     *
     * @var string
     */
    public $featureSlug;
    /**
     * User's id.
     *
     * @var int
     */
    public $userId;
    /**
     * Feature's property value (user input).
     *
     * @var object
     */
    public $value;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Feature\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['featureSlug'])) {
            $this->featureSlug = $parameters['featureSlug'];
        }

        return $this;
    }
}
