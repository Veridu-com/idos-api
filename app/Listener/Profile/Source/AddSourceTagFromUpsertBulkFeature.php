<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Source;

use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use App\Repository\RepositoryInterface;
use Interop\Container\ContainerInterface;
use League\Event\EventInterface;

/**
 * This listener is responsible to add to the source tags
 * "profilePicture" and  "profileURL".
 *
 * This listener is called after \App\Event\Profile\Feature\CreatedBulk event was fired.
 */
class AddSourceTagFromUpsertBulkFeature extends AbstractListener {
    /**
     * Source repository.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $sourceRepository;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Listener\Profile\Source\AddSourceTagFromUpsertBulkFeature(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Source')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryInterface $sourceRepository The source repository
     *
     * @return void
     */
    public function __construct(RepositoryInterface $sourceRepository) {
        $this->sourceRepository = $sourceRepository;
    }

    public function handle(EventInterface $event) {
        foreach ($event->features as $feature) {
            if (! $event->source) {
                continue;
            }

            if (empty($feature->value)) {
                continue;
            }

            switch ($feature->name) {
                case 'profilePicture':
                    $event->source->setTag('profile_picture', $feature->value);
                    $this->sourceRepository->save($event->source);
                    break;
                case 'profileId':
                    $event->source->setTag('profile_id', $feature->value);
                    $this->sourceRepository->save($event->source);
                    break;
                case 'profileUrl':
                    $event->source->setTag('profile_url', $feature->value);
                    $this->sourceRepository->save($event->source);
                    break;
            }
        }
    }
}
