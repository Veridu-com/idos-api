<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Review;
use App\Exception\NotFound;
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
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndWarningIds(int $userId, array $warningIds) : Collection {
        $result = $this->query()
            ->selectRaw('reviews.*')
            ->where('user_id', '=', $userId);

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
    public function findOneByUserIdAndId(int $userId, int $id) : Review {
        $result = $this->findBy(['user_id' => $userId, 'id' => $id]);

        if ($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();
    }
}
