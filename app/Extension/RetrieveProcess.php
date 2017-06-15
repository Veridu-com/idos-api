<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Extension;

use App\Entity\Profile\Process;
use App\Exception\NotFound;
use App\Repository\Profile\ProcessInterface;

/**
 * Trait to retrieve a User's process.
 */
trait RetrieveProcess {
    /**
     * Gets the related process.
     *
     * @param \App\Repository\Profile\ProcessInterface $processRepository The process repository
     * @param int                                      $userId            The user identifier
     * @param string                                   $event             The event name
     * @param mixed                                    $source            The source
     *
     * @return \App\Entity\Profile\Process
     */
    private function getRelatedProcess(ProcessInterface $processRepository, int $userId, string $event, $source = null) : Process {
        $sourceId = $source ? $source->id : null;

        // tries to find an existing process
        try {
            if ($source) {
                return $processRepository->findOneBySourceId($sourceId);
            }

            return $processRepository->findLastByUserIdSourceIdAndEvent($event, $sourceId, $userId);
        } catch (NotFound $exception) {
            // creates a new process if not found
            $entity = $processRepository->create(
                [
                    'name'      => 'idos:verification',
                    'user_id'   => $userId,
                    'source_id' => $sourceId,
                    'event'     => $event
                ]
            );

            return $processRepository->save($entity);
        }
    }
}
