<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Source;

use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use App\Repository\Profile\SourceInterface;
use Interop\Container\ContainerInterface;
use League\Event\EventInterface;

/**
 * This listener is responsible to add to the source tags
 * "profilePicture" and "profileURL".
 *
 * This listener is called after the \App\Event\Profile\Feature\Created event was fired.
 */
class AddSourceTagFromCreateFeature extends AbstractListener {
    /**
     * Source repository.
     *
     * @var \App\Repository\Profile\SourceInterface
     */
    private $sourceRepository;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            return new \App\Listener\Profile\Source\AddSourceTagFromCreateFeature(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Source')
            );
        };
    }

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
        if (! $event->source) {
            return;
        }

        if (empty($event->feature->value)) {
            return;
        }

        switch ($event->feature->name) {
            case 'profilePicture':
                $event->source->setTag('profile_picture', $event->feature->value);
                $this->sourceRepository->save($event->source);
                break;
            case 'profileId':
                $event->source->setTag('profile_id', $event->feature->value);
                $this->sourceRepository->save($event->source);
                break;
            case 'profileUrl':
                $event->source->setTag('profile_url', $event->feature->value);
                $this->sourceRepository->save($event->source);
                break;
        }
    }
}
