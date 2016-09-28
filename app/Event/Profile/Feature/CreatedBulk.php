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
     * Event related User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Event related Credential.
     *
     * @var App\Entity\Company\Credential
     */
    public $credential;

    /**
     * Event related Credential.
     *
     * @var App\Entity\Profile\Source
     */
    public $source;

    /**
     * Class constructor.
     *
     * @param App\Entity\User                  $user
     * @param App\Entity\Company\Credential    $credential
     * @param App\Entity\Profile\Source | null $source
     *
     * @return void
     */
    public function __construct(User $user, Credential $credential, $source = null) {
        $this->user         = $user;
        $this->credential   = $credential;
        $this->source   = $source;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        return array_merge(
            [
                'publicKey' => $this->credential->public,
                'userName'  => $this->user->username,
                'source'    => $this->source ? $this->source->name : null,
                'processId' => 1, // @FIXME process creation process must be reviewed
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
