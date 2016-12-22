<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Reference;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple references.
 */
class DeletedMulti extends AbstractEvent implements UserIdGetterInterface {
    /**
     * Event related References.
     *
     * @var \Illuminate\Support\Collection
     */
    public $references;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $references
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Collection $references, Credential $credential) {
        $this->references = $references;
        $this->credential = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : int {
        if ($this->references->isEmpty()) {
            throw new \RuntimeException('No rows affected.');
        }

        return $this->references->first()->userId;
    }
}
