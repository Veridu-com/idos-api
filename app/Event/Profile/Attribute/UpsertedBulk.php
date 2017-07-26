<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Attribute;

use App\Entity\Company\Credential;
use App\Entity\User;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;

/**
 * UpsertedBulk event.
 */
class UpsertedBulk extends AbstractEvent implements UserIdGetterInterface{
    /**
     * Event related Attributes.
     *
     * @var array
     */
    public $attributes;
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param array                          $attributes
     * @param \App\Entity\User               $user
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(array $attributes, User $user, Credential $credential) {
        $this->attributes  = $attributes;
        $this->user        = $user;
        $this->credential  = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : int {
        return $this->user->id;
    }
}
