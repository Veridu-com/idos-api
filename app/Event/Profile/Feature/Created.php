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
 * Created event.
 */
class Created extends AbstractServiceQueueEvent {
    /**
     * Event related Feature.
     *
     * @var App\Entity\Profile\Feature
     */
    public $feature;

    /**
     * Event related Source.
     *
     * @var App\Entity\Profile\Source | null
     */
    public $source;

    /**
     * Event related User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Event related Process.
     *
     * @var App\Entity\Profile\Process
     */
    public $process;

    /**
     * Class constructor.
     *
     * @param App\Entity\Profile\Feature     $feature
     * @param App\Entity\User                $user
     * @param App\Entity\Company\Credential  $credential
     * @param App\Entity\Profile\Process     $process
     * @param App\Entity\Profile\Source|null $source
     *
     * @return void
     */
    public function __construct(Feature $feature, User $user, Credential $credential, Process $process, $source = null) {
        $this->feature     = $feature;
        $this->user        = $user;
        $this->source      = $source;
        $this->process     = $process;
        $this->credential  = $credential;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {

        return array_merge(
            [
            'providerName' => $this->source ? $this->source->name : null,
            'sourceId'     => $this->source ? $this->source->getEncodedId() : null,
            'publicKey'    => $this->credential->public,
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
        return sprintf('idos:feature.%s.created', $this->source ? $this->source->name : 'profile');
    }
}
