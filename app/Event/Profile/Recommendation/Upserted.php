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
use App\Entity\Service;
use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Upserted event.
 */
class Upserted extends AbstractEvent {
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
     * Event related Service.
     *
     * @var \App\Entity\Service
     */
    public $service;
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
     * @param \App\Entity\Company\Credential     $credential
     *
     * @return void
     */
    public function __construct(Recommendation $recommendation, User $user, Service $service, Company $company, Credential $credential) {
        $this->recommendation = $recommendation;
        $this->user           = $user;
        $this->service        = $service;
        $this->company        = $company;
        $this->credential     = $credential;
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        return sprintf('idos:recommendation.%s.upserted', $this->user->id);
    }
}
