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
 * "profilePicture" and "profileURL".
 *
 * This listener is called after the \App\Event\Profile\Feature\Created event is fired.
 */
class AddSourceTagFromCreateFeatureListener extends AbstractListener {
    /**
     * Source repository.
     *
     * @var \App\Repository\Profile\SourceInterface
     */
    private $sourceRepository;

    public function __construct(SourceInterface $sourceRepository) {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * { function_description }.
     *
     * @param \League\Event\EventInterface $event The event
     */
    public function handle(EventInterface $event) {
        if (! $event->source) {
            return;
        }

        if ($event->feature->name == 'profilePicture') {
            $event->source->setTag('profile_picture', $event->feature->value);
            $this->sourceRepository->save($event->source);

            return;
        }

        if ($event->feature->name == 'profileId') {
            $event->source->setTag('profile_id', $event->feature->value);
            $this->sourceRepository->save($event->source);

            return;
        }

        if ($event->feature->name == 'profileURL') {
            $event->source->setTag('profile_url', $event->feature->value);
            $this->sourceRepository->save($event->source);

            return;
        }
    }
}
