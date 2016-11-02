<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Company\Invitation;

use App\Entity\Company\Credential;
use App\Entity\Company\Invitation;
use App\Event\AbstractServiceQueueEvent;

/**
 * UpdatedWithResend event.
 */
class UpdatedWithResend extends AbstractServiceQueueEvent {
    /**
     * Event related Member.
     *
     * @var \App\Entity\Company\Invitation
     */
    public $invitation;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Event related Company name. Dashboard's owner.
     *
     * @var string
     */
    public $companyName;
    /**
     * Event related dashboard name.
     *
     * @var string
     */
    public $dashboardName;
    /**
     * Event related signup hash.
     *
     * @var string
     */
    public $signupHash;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Company\Member $member
     *
     * @return void
     */
    public function __construct(Invitation $invitation, Credential $credential, string $companyName, string $dashboardName, string $signupHash) {
        $this->invitation    = $invitation;
        $this->credential    = $credential;
        $this->companyName   = $companyName;
        $this->dashboardName = $dashboardName;
        $this->signupHash    = $signupHash;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge(
            [
                'user' => [
                    'name'  => $this->invitation->name,
                    'email' => $this->invitation->email
                ],
                'invitation'    => $this->invitation->toArray(),
                'dashboardName' => $this->dashboardName,
                'companyName'   => $this->companyName,
                'signupHash'    => $this->signupHash
            ], $merge
        );
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        return 'idos:invitation.updated-with-resend';
    }
}
