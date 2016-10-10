<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Review;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Review Repository Implementation.
 */
class DBReview extends AbstractSQLDBRepository implements ReviewInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'reviews';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Review';

    /**
     * {@inheritdoc}
     */
    public function findOne(int $id, int $identityId, int $userId) : Review {
        return $this->findOneBy(
            [
            'id'          => $id,
            'user_id'     => $userId,
            'identity_id' => $identityId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserIdAndIdentityId(int $identityId, int $userId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
            'user_id'     => $userId,
            'identity_id' => $identityId
            ], $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }
}
