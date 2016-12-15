<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Entity\Company\Credential;
use App\Factory\Event as EventFactory;
use App\Listener;
use App\Listener\AbstractListener;
use App\Listener\QueueCompanyServiceHandlers;
use App\Repository\Company\CredentialInterface;
use App\Repository\ServiceInterface;
use League\Event\Emitter;
use League\Event\EventInterface;

/**
 * This listener is responsible for sending to the "Manager"
 * all Service related tasks.
 */
class QueueServiceTaskListener extends AbstractListener {
    use QueueCompanyServiceHandlers;
    /**
     * Company id.
     *
     * @var int|null
     */
    private $companyId;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Service Handler Repository instance.
     *
     * @var \App\Repository\ServiceInterface
     */
    private $serviceRepository;
    /**
     * Event Factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event Emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;
    /**
     * Gearman Client instance.
     *
     * @var \GearmanClient
     */
    private $gearmanClient;

    /**
     * Class constructor.
     *
     * @param \App\Repository\CredentialInterface     $credentialRepository
     * @param \App\Repository\ServiceInterface $serviceRepository
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     * @param \GearmanClient                          $gearmanClient
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $credentialRepository,
        ServiceInterface $serviceRepository,
        EventFactory $eventFactory,
        Emitter $emitter,
        \GearmanClient $gearmanClient
    ) {
        $this->credentialRepository     = $credentialRepository;
        $this->serviceRepository = $serviceRepository;
        $this->eventFactory             = $eventFactory;
        $this->emitter                  = $emitter;
        $this->gearmanClient            = $gearmanClient;
    }

    /**
     * Handles events that trigger data scraping.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $this->queueListeningServices($event->credential->companyId, $event);
    }
}
