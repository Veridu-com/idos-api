<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\ServiceHandler;

use App\Command\AbstractCommand;

/**
 * ServiceHandler "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * ServiceHandler's slug.
     *
     * @var string
     */
    public $slug;

    /**
     * ServiceHandler company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * ServiceHandler service's slug.
     *
     * @var int
     */
    public $serviceSlug;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\ServiceHandler\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['slug'])) {
            $this->slug = $parameters['slug'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        if (isset($parameters['service'])) {
            $this->serviceSlug = $parameters['service'];
        }

        return $this;
    }
}
