<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Flag;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple flags.
 */
class DeletedMulti extends AbstractEvent implements UserIdGetterInterface {
    /**
     * Event related Flags.
     *
     * @var \Illuminate\Support\Collection
     */
    public $flags;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $flags
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Collection $flags, Credential $credential) {
        $this->flags      = $flags;
        $this->credential = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : int {
        if ($this->flags->isEmpty()) {
            throw new \RuntimeException('No rows affected.');
        }
        return $this->flags->first()->userId;
    }
}
