<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Feature SLUG.
     *
     * @var string
     */
    public $featureSlug;
    /**
     * User Id.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Feature\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['featureSlug'])) {
            $this->featureSlug = $parameters['featureSlug'];
        }

        return $this;
    }
}
