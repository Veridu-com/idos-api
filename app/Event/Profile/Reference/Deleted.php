<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Reference;

use App\Entity\Company\Credential;
use App\Entity\Profile\Reference;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;

/**
 * Deleted event.
 */
class Deleted extends AbstractEvent implements UserIdGetterInterface {
    /**
     * Event related Reference.
     *
     * @var \App\Entity\Profile\Reference
     */
    public $reference;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Reference  $reference
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Reference $reference, Credential $credential) {
        $this->reference  = $reference;
        $this->credential = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : int {
        return $this->reference->userId;
    }
}
