<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Extension;

/**
 * Queues on Manager Gearman.
 */
trait QueuesOnManager {
    /**
     * Queue work on the "manager" work queue.
     *
     * @param string $payload Payload to be sent
     *
     * @return bool
     */
    private function queueOnManager(\GearmanClient $gearmanClient, array $payload) : bool {
        $gearmanClient->doBackground(
            'manager',
            json_encode($payload),
            uniqid('manager-')
        );

        return $gearmanClient->returnCode() == \GEARMAN_SUCCESS;
    }
}
