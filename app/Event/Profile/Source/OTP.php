<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Entity\Company;
use App\Entity\Company\Credential;
use App\Entity\Profile\Process;
use App\Entity\Profile\Source;
use App\Entity\User;
use App\Event\AbstractServiceQueueEvent;

/**
 * OTP event.
 */
class OTP extends AbstractServiceQueueEvent {
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Source.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Event related Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Event related Process.
     *
     * @var \App\Entity\Profile\Process
     */
    public $process;
    /**
     * Event related IP Address.
     *
     * @var string
     */
    public $ipAddr;
    /**
     * OTP Check type.
     *
     * @var string
     */
    private $type;
    /**
     * Source tags.
     *
     * @var \stdClass
     */
    private $sourceTags;
    /**
     * OTP Check target value.
     *
     * @var string
     */
    private $target;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Source     $source
     * @param \App\Entity\User               $user
     * @param \App\Entity\Company\Credential $credential
     * @param \App\Entity\Company            $company
     * @param \App\Entity\Profile\Process    $process
     * @param string                         $ipAddr
     *
     * @return void
     */
    public function __construct(Source $source, User $user, Credential $credential, Company $company, Process $process, string $ipAddr) {
        $this->source     = $source;
        $this->user       = $user;
        $this->credential = $credential;
        $this->company    = $company;
        $this->process    = $process;
        $this->ipAddr     = $ipAddr;

        $sourceArray      = $source->serialize();
        $this->sourceTags = json_decode($sourceArray['tags']);

        if (property_exists($this->sourceTags, 'email')) {
            $this->type   = 'email';
            $this->target = $this->sourceTags->email;
        }

        if (property_exists($this->sourceTags, 'phone')) {
            $this->type   = 'phone';
            $this->target = $this->sourceTags->phone;
        }
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge(
            [
                'target'    => $this->target,
                'password'  => $this->sourceTags->otp_code,
                'publicKey' => $this->credential->public,
                'sourceId'  => $this->source->getEncodedId(),
                'processId' => $this->process->getEncodedId(),
                'company'   => $this->company->toArray(),
                'userName'  => $this->user->username
            ],
            $merge
        );
    }

    /**
     * {inheritdoc}.
     */
    public function __toString() {
        return sprintf('idos:otp.%s.created', $this->type);
    }
}
