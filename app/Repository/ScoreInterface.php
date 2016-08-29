<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Score;
use Illuminate\Support\Collection;

/**
 * Score Repository Interface.
 */
interface ScoreInterface extends RepositoryInterface {
    /**
     * Gets all Score entities based on their user_id and attribute name.
     *
     * @param int $userId
     * @param string $attributeName
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAndAttributeName(int $userId, string $attributeName) : Collection;
    /**
     * Gets all Score entities based on their user_id, attribute name and filtering them by name.
     *
     * @param int   $userId
     * @param string   $attributeName
     * @param array $names
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByUserIdAttributeNameAndNames(int $userId, string $attributeName, array $names) : Collection;
    /*
     * Deletes all Score entities based on attribute name.
     *
     * @param int $attributeName
     *
     * @return int
     */
    public function deleteByAttributeName(string $attributeName) : int;
    /**
     * Find a Score entity based on its user_id, attribute name and name.
     *
     * @param string    $attributeName
     * @param string $name
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Score
     */
    public function findOneByUserIdAttributeNameAndName(int $userId, string $attributeName, string $name) : Score;
    /**
     * Deletes a Score entity based on their attribute name and name.
     *
     * @param string    $attributeName
     * @param string $name
     *
     * @return int
     */
    public function deleteOneByAttributeNameAndName(string $attributeName, string $name) : int;
}
