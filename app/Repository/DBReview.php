<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Review;
use App\Exception\NotFound;
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
    protected $entityName = 'Review';

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndWarningIdsAndIdentity(int $userId, array $warningIds, int $identityId) : Collection {
        $result = $this->query()
            ->selectRaw('reviews.*')
            ->where('user_id', '=', $userId)
            ->where('identity_id', '=', $identityId);

        if (! empty($warningIds)) {
            $warningIds = array_map([$this->optimus, 'decode'], $warningIds);
            $result     = $result->whereIn('reviews.warning_id', $warningIds);
        }

        $result = $result->get();

        return new Collection($result);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUserIdAndIdAndIdentityId(int $userId, int $id, int $identityId) : Review {
        $result = $this->findBy(
            [
            'user_id'     => $userId,
            'identity_id' => $identityId,
            'id'          => $id
            ]
        );

        if ($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }
}
