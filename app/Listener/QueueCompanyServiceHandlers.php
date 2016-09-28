<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener;

use App\Event\ServiceQueueEventInterface;
use League\Event\EventInterface;

trait QueueCompanyServiceHandlers {
    /**
     * Queues Service Handlers' tasks for the given event and company.
     *
     * @param int                                  $companyId    Company identifier
     * @param App\Event\ServiceQueueEventInterface $event        Event identifier
     * @param array                                $mergePayload Payload that will be merged into "handler" property
     *
     * @return bool
     */
    public function queueListeningServices(int $companyId, ServiceQueueEventInterface $event, array $mergePayload = []) : bool {
        // find handlers
        $handlers = $this->serviceHandlerRepository->getAllByCompanyIdAndListener($companyId, (string) $event);

        if ($handlers->isEmpty()) {
            $this->dispatchUnhandleEvent($event);

            return false;
        }

        $success = true;
        foreach ($handlers as $handler) {
            $service = $handler->service();

            // create payload
            $payload = [
                'name'    => $service->name,
                'user'    => $service->authUsername,
                'pass'    => $service->authPassword,
                'url'     => $service->url,
                'handler' => $event->getServiceHandlerPayload($mergePayload)
            ];

            if ($this->queue($payload)) {
                $this->emitter->emit($this->eventFactory->create('Manager\\WorkQueued', $event));
                continue;
            }

            $success = false;
            $this->dispatchUnhandleEvent($event);
        }

        return $success;
    }

    /**
     * Queue work on the "manager" work queue.
     *
     * @param string $payload Payload to be sent
     *
     * @return bool
     */
    private function queue(array $payload) : bool {
        $task = $this->gearmanClient->doBackground(
            sprintf('idos-manager-%s', str_replace('.', '', __VERSION__)),
            json_encode($payload)
        );

        return $this->gearmanClient->returnCode() == \GEARMAN_SUCCESS;
    }

    /**
     * Dispatches an unhandled event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    private function dispatchUnhandleEvent(EventInterface $event) {
        $unhandledEvent = $this->eventFactory->create(
            'Manager\\UnhandledEvent',
            $event
        );
        $this->emitter->emit($unhandledEvent);
    }
}
