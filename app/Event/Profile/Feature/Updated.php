<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Feature;

use App\Entity\Company\Credential;
use App\Entity\Profile\Feature;
use App\Entity\Profile\Process;
use App\Entity\Profile\Source;
use App\Entity\User;
use App\Event\AbstractServiceQueueEvent;

/**
 * Updated event.
 */
class Updated extends AbstractServiceQueueEvent {
    /**
     * Event related Feature.
     *
     * @var \App\Entity\Profile\Feature
     */
    public $feature;
    /**
     * Event related Source.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Process.
     *
     * @var \App\Entity\Profile\Process
     */
    public $process;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Feature     $feature
     * @param \App\Entity\User                $user
     * @param \App\Entity\Company\Credential  $credential
     * @param \App\Entity\Profile\Process     $process
     * @param \App\Entity\Profile\Source|null $source
     *
     * @return void
     */
    public function __construct(Feature $feature, User $user, Process $process, Credential $actor, $source = null) {
        $this->feature     = $feature;
        $this->user        = $user;
        $this->process     = $process;
        $this->actor       = $actor;
        $this->source      = $source;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {

        return array_merge(
            [
            'providerName' => $this->source ? $this->source->name : null,
            'sourceId'     => $this->source ? $this->source->getEncodedId() : null,
            'publicKey'    => $this->actor->public,
            'processId'    => $this->process->getEncodedId(),
            'userName'     => $this->user->username
            ], $merge
        );
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        return sprintf('idos:feature.%s.updated', $this->source ? $this->source->name : 'profile');
    }
}
