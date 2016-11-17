<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Widget;

use App\Command\AbstractCommand;

/**
 * Widget "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Widget label.
     *
     * @var string
     */
    public $label;
    /**
     * Widget type.
     *
     * @var string
     */
    public $type;
    /**
     * Widget config.
     *
     * @var string
     */
    public $config;
    /**
     * Widget enabled.
     *
     * @var bool
     */
    public $enabled;
    /**
     * Target company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Credential id.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\Widget\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['label'])) {
            $this->label = $parameters['label'];
        }

        if (isset($parameters['config'])) {
            $this->config = $parameters['config'];
        }

        if (isset($parameters['type'])) {
            $this->type = $parameters['type'];
        }

        if (isset($parameters['credential_public'])) {
            $this->credentialPubKey = $parameters['credential_public'];
        }

        if (isset($parameters['enabled'])) {
            $this->enabled = $parameters['enabled'];
        }

        return $this;
    }
}
