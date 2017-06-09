<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Factory\Entity;
use App\Helper\Vault;
use Jenssegers\Optimus\Optimus;
use League\Flysystem\Filesystem;

/**
 * File-based Repository Strategy.
 */
class FileStrategy implements RepositoryStrategyInterface {
    /**
     * Entity Factory.
     *
     * @var \App\Factory\Entity
     */
    private $entityFactory;
    /**
     * File System instance.
     *
     * @var \League\Flysystem\Filesystem
     */
    private $fileSystem;
    /**
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    private $optimus;
    /**
     * Vault helper.
     *
     * @var \App\Helper\Vault
     */
    private $vault;

    /**
     * Class constructor.
     *
     * @param \App\Factory\Entity          $entityFactory
     * @param \League\Flysystem\Filesystem $fileSystem
     * @param \Jenssegers\Optimus\Optimus  $optimus
     * @param \App\Helper\Vault            $vault
     *
     * @return void
     */
    public function __construct(
        Entity $entityFactory,
        Filesystem $fileSystem,
        Optimus $optimus,
        Vault $vault
    ) {
        $this->entityFactory = $entityFactory;
        $this->fileSystem    = $fileSystem;
        $this->optimus       = $optimus;
        $this->vault         = $vault;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormattedName(string $repositoryName) : string {
        static $cache = [];

        if (isset($cache[$repositoryName])) {
            return $cache[$repositoryName];
        }

        $splitName = explode('\\', $repositoryName);

        if (is_array($splitName) && count($splitName) > 1) {
            $name                   = array_pop($splitName);
            $namespace              = implode('\\', $splitName);
            $formattedName          = sprintf('%s\\File%s', $namespace, ucfirst($name));
            $cache[$repositoryName] = $formattedName;

            return $formattedName;
        }

        $formattedName          = sprintf('File%s', ucfirst($repositoryName));
        $cache[$repositoryName] = $formattedName;

        return $formattedName;
    }

    /**
     * {@inheritdoc}
     */
    public function build(Repository $repositoryFactory, string $className) : RepositoryInterface {
        static $cache = [];

        return new $className(
            $this->entityFactory,
            $this->fileSystem,
            $this->optimus,
            $this->vault
        );
    }
}
