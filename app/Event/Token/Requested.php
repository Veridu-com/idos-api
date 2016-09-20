<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Token;

use App\Entity\Company;
use App\Entity\Company\Credential;
use App\Entity\User;
use App\Event\AbstractEvent;

/**
 * Requested event.
 */
class Requested extends AbstractEvent {
    /**
     * User who requested the exchange.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * The company in which the user currently is authenticated.
     *
     * @var App\Entity\Company
     */
    public $actingCompany;
    /**
     * The company that the user wants to impersonate.
     *
     * @var App\Entity\Company
     */
    public $targetCompany;
    /**
     * Credential corresponding to the authenticated user.
     *
     * @var App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param App\Entity\User       $user
     * @param App\Entity\Company    $actingCompany
     * @param App\Entity\Company    $targetCompany
     * @param App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(User $user, Company $actingCompany, Company $targetCompany, Credential $credential) {
        $this->user          = $user;
        $this->actingCompany = $actingCompany;
        $this->targetCompany = $targetCompany;
        $this->credential    = $credential;
    }
}
