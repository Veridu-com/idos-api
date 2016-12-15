<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Recommendation;

use App\Entity\Company;
use App\Entity\Company\Credential;
use App\Entity\Profile\Recommendation;
use App\Entity\Handler;
use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Updated event.
 */
class Updated extends AbstractEvent {
    /**
     * Event related Recommendation.
     *
     * @var \App\Entity\Profile\Recommendation
     */
    public $recommendation;
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Handler.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Event related Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Recommendation $recommendation
     * @param \App\Entity\User                   $user
     * @param \App\Entity\Handler                $handler
     * @param \App\Entity\Company                $company
     * @param \App\Entity\Company\Credential     $credential
     *
     * @return void
     */
    public function __construct(Recommendation $recommendation, User $user, Handler $handler, Company $company, Credential $credential) {
        $this->recommendation = $recommendation;
        $this->user           = $user;
        $this->handler        = $handler;
        $this->company        = $company;
        $this->credential     = $credential;
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        return sprintf('idos:recommendation.%s.updated', $this->user->id);
    }
}
