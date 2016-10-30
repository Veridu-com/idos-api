<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Subscription;

use App\Command\AbstractCommand;

/**
 * Subscription "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Subscription category slug.
     *
     * @var string
     */
    public $categorySlug;
    /**
     * Acting Credential.
     *
     * @var \App\Entity\Credential
     */
    public $credential;
    /**
     * Subscription's credential's public key.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Acting Identity.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\Subscription\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['category_slug'])) {
            $this->categorySlug = $parameters['category_slug'];
        }

        if (isset($parameters['identity'])) {
            $this->identity = $parameters['identity'];
        }

        if (isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
