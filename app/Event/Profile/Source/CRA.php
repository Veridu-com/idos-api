<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Entity\Company\Credential;
use App\Entity\Profile\Source;
use App\Entity\User;
use App\Event\AbstractServiceQueueEvent;

/**
 * CRA event.
 */
class CRA extends AbstractServiceQueueEvent {
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
     * Event related IP Address.
     *
     * @var string
     */
    public $ipAddr;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Class constructor.
     *
     * @param \App\Entity\User               $user
     * @param \App\Entity\Profile\Source     $source
     * @param string                         $ipAddr
     * @param \App\Entity\Company\Credential $credential
     *
     * @return void
     */
    public function __construct(User $user, Source $source, string $ipAddr, Credential $credential) {
        $this->user       = $user;
        $this->source     = $source;
        $this->ipAddr     = $ipAddr;
        $this->credential = $credential;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge(
            [
            'publicKey' => $this->credential->public,
            'sourceId'  => $this->source->getEncodedId(),
            'userName'  => $this->user->username,
            'data'      => $this->source->tags
            ], $merge
        );
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        if (! $this->source->tags || ! property_exists($this->source->tags, 'provider')) {
            return 'idos:cra';
        }

        return sprintf('idos:cra.%s', strtolower($this->source->tags->provider));
    }
}
