<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Feature;

use App\Entity\Company\Credential;
use App\Entity\Profile\Feature;
use App\Entity\Profile\Source;
use App\Entity\Service;
use App\Entity\User;
use App\Event\AbstractServiceQueueEvent;

/**
 * CreatedBulk event.
 */
class CreatedBulk extends AbstractServiceQueueEvent {
    /**
     * Event related Features.
     *
     * @var array
     */
    public $features;

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
     * Class constructor.
     *
     * @param array                          $features
     * @param App\Entity\Service             $service
     * @param App\Entity\User                $user
     * @param App\Entity\Company\Credential  $credential
     * @param App\Entity\Profile\Source|null $source
     *
     * @return void
     */
    public function __construct(array $features, Service $service, User $user, Credential $credential, $source = null) {
        $this->features     = $features;
        $this->service      = $service;
        $this->user         = $user;
        $this->credential   = $credential;
        $this->source       = $source;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge(
            [
                'features'    => $this->features,
                'sourceId'    => $this->source ? $this->source->id : null,
                'publicKey'   => $this->credential->public,
                'processId'   => 1, // @FIXME process creation process must be reviewed
                'userName'    => $this->user->username
            ], $merge
        );
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        // @FIXME double check event identifier
        // does it can have or need $feature->name to be part of it?
        return sprintf('idos:feature.%s.created', $this->source ? $this->source->name : 'profile');
    }
}
