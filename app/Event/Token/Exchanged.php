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
 * Exchanged event.
 */
class Exchanged extends AbstractEvent {
    /**
     * User who requested the exchange.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Highest role user found with the same identity.
     *
     * @var App\Entity\User
     */
    public $highestRoleUser;
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
     * @param App\Entity\User       $highestRoleUser
     * @param App\Entity\Company    $actingCompany
     * @param App\Entity\Company    $targetCompany
     * @param App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(User $user, User $highestRoleUser, Company $actingCompany, Company $targetCompany, Credential $credential) {
        $this->user            = $user;
        $this->highestRoleUser = $highestRoleUser;
        $this->actingCompany   = $actingCompany;
        $this->targetCompany   = $targetCompany;
        $this->credential      = $credential;
    }
}
