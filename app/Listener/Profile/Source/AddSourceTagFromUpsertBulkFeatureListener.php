<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Source;

use App\Listener\AbstractListener;
use App\Repository\Profile\SourceInterface;
use League\Event\EventInterface;

/**
 * This listener is responsible to add to the source tags
 * "profilePicture" and  "profileURL".
 *
 * This listener is called after \App\Event\Profile\Feature\CreatedBulk event is fired.
 */
class AddSourceTagFromUpsertBulkFeatureListener extends AbstractListener {
    /**
     * Source repository.
     *
     * @var \App\Repository\Profile\SourceInterface
     */
    private $sourceRepository;

    /**
     * Class constructor.
     *
     * @param \App\Repository\Profile\SourceInterface $sourceRepository The source repository
     *
     * @return void
     */
    public function __construct(SourceInterface $sourceRepository) {
        $this->sourceRepository = $sourceRepository;
    }

    public function handle(EventInterface $event) {
        foreach ($event->features as $feature) {
            if (! $event->source) {
                continue;
            }

            if ($feature['name'] == 'profilePicture') {
                $event->source->setTag('profile_picture', $feature['value']);
                $this->sourceRepository->save($event->source);
                continue;
            }

            if ($feature['name'] == 'profileId') {
                $event->source->setTag('profile_id', $feature['value']);
                $this->sourceRepository->save($event->source);
                continue;
            }

            if ($feature['name'] == 'profileURL') {
                $event->source->setTag('profile_url', $feature['value']);
                $this->sourceRepository->save($event->source);
                continue;
            }
        }
    }
}
