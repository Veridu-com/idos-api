<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Attribute;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple Attributes.
 */
class DeletedMulti extends AbstractEvent implements UserIdGetterInterface {
    /**
     * Event related Attributes.
     *
     * @var \Illuminate\Support\Collection
     */
    public $attributes;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $attributes
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Collection $attributes, Credential $credential) {
        $this->attributes = $attributes;
        $this->credential = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : int {
        if ($this->attributes->isEmpty()) {
            throw new \RuntimeException('No rows affected.');
        }
        return $this->attributes->first()->userId;
    }
}
