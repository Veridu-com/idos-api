<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Source;

use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use Interop\Container\ContainerInterface;
use League\Event\EventInterface;
use League\Flysystem\Filesystem;

/**
 * This listener is responsible to send file uploads to AWS S3.
 *
 * This listener is called after the \App\Event\Profile\Source\File event was fired.
 */
class SaveFileToStorage extends AbstractListener {
    /**
     * File System instance.
     *
     * @var \League\Flysystem\Filesystem
     */
    private $fileSystem;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            $fileSystem = $container->get('fileSystem');

            return new \App\Listener\Profile\Source\SaveFileToStorage(
                $fileSystem('source')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \League\Flysystem\Filesystem $fileSystem
     *
     * @return void
     */
    public function __construct($fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function handle(EventInterface $event) {
            $this->fileSystem->write(
                sprintf(
                    '%s/%s/%s/%s.%s',
                    $event->company->id,
                    $event->credential->id,
                    $event->user->id,
                    $event->source->id,
                    $event->fileExtension
                ),
                $event->fileContents
            );
    }
}
