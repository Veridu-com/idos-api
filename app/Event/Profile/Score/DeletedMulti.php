<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Score;

use App\Entity\Company\Credential;
use App\Event\AbstractEvent;
use App\Event\Interfaces\UserIdGetterInterface;
use Illuminate\Support\Collection;

/**
 * Deleted event for multiple scores.
 */
class DeletedMulti extends AbstractEvent implements UserIdGetterInterface {
    /**
     * Event related Scores.
     *
     * @var \Illuminate\Support\Collection
     */
    public $scores;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Support\Collection $scores
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(Collection $scores, Credential $credential) {
        $this->scores     = $scores;
        $this->credential = $credential;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId() : int {
        if ($this->scores->isEmpty()) {
            throw new \RuntimeException('No rows affected.');
        }

        return $this->scores->first()->userId;
    }
}
