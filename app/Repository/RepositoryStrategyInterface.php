<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Repository Strategy Interface.
 */
interface RepositoryStrategyInterface {
    /**
     * Gets the repository's formatted name.
     *
     * @param string $repositoryName
     *
     * @return string
     */
    public function getFormattedName($repositoryName);

    /**
     * Builds a new repository.
     *
     * @param string $className
     * @param string $repositoryName
     *
     * @return App\Repository\RepositoryInterface
     */
    public function build($className, $repositoryName);
}
