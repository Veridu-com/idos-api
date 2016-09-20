<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Score;
use Illuminate\Support\Collection;
use App\Repository\RepositoryInterface;

/**
 * Score Repository Interface.
 */
interface ScoreInterface extends RepositoryInterface {
    /**
     * Returns all features based on their user id.
     *
     * @param int   $userId
     * @param array $queryParams
     *
     * @return Collection
     */
    public function findByUserId(int $userId, array $queryParams = []) : Collection;

    public function findOneByName(int $userId, int $serviceId, string $name) : Score;

    /**
     * Gets all Score entities based on their user_id, attribute name and filtering them by name.
     *
     * @param int    $userId
     * @param string $attributeName
     * @param array  $queryParams
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAttributeNameAndNames(int $userId, string $attributeName, array $queryParams = []) : Collection;
    /*
     * Deletes all Score entities based on attribute id.
     *
     * @param int $attributeId
     *
     * @return int
     */
    public function deleteByAttributeId(int $attributeId) : int;
    /**
     * Find a Score entity based on its user_id, attribute name and name.
     *
     * @param string $attributeName
     * @param string $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Profile\Score
     */
    public function findOneByUserIdAttributeNameAndName(int $userId, string $attributeName, string $name) : Score;
    /**
     * Deletes a Score entity based on their attribute id and name.
     *
     * @param int    $attributeId
     * @param string $name
     *
     * @return int
     */
    public function deleteOneByAttributeIdAndName(int $attributeId, string $name) : int;
}
