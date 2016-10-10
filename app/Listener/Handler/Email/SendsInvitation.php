<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Handler\Email;

use App\Event\Company\Member\InvitationCreated;
use App\Listener\AbstractListener;
use App\Listener\QueueCompanyServiceHandlers;
use League\Event\EventInterface;

class SendsInvitation extends AbstractListener {
	use QueueCompanyServiceHandlers;

	private $gearmanClient;

    public function __construct(\GearmanClient $gearmanClient) {
    	$this->gearmanClient = $gearmanClient;
    }

    public function handle(EventInterface $event) {
        // create payload
        $payload = [
            'name'    => 'idOS Email handler',
            'user'    => 'idos',
            'pass'    => 'idos',
            'url'     => 'email.idos.io:8082',
            'handler' => [
            	'email' => 'dashboard.invitation',
            	'invitation' => $event->invitation->serialize()
            ]
        ];

        return $this->queue($payload);
    }
}
