<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Recommendation;
use App\Repository\AbstractSQLDBRepository;

/**
 * Database-based Recommendation Repository Implementation.
 */
class DBRecommendation extends AbstractSQLDBRepository implements RecommendationInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'recommendations';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Recommendation';

    /**
     * {@inheritdoc}
     */
    public function findOne(int $userId) : Recommendation {
        return $this->findOneBy(
            [
                'user_id' => $userId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function upsertOne(int $userId, int $serviceId, bool $result, array $reasons) : Recommendation {
        $this->beginTransaction();

        $result = $this->runRaw(
            'INSERT INTO "recommendations" ("creator", "user_id", "result", "reasons", "created_at") VALUES (:creator, :user_id, :result, :reasons, :created_at)
            ON CONFLICT ("user_id") DO UPDATE SET "result" = :result, "reasons" = :reasons, "updated_at" = :updated_at',
            [

                'creator'     => $serviceId,
                'user_id'     => $userId,
                'result'      => $result,
                'reasons'     => json_encode($reasons),
                'created_at'  => date('Y-m-d H:i:s', time()),
                'updated_at'  => date('Y-m-d H:i:s', time())
            ]
        );

        if (! $result) {
            $this->rollBack();
            throw new Create\Profile\RecommendationException('Error while trying to upsert a recommendation', 500);
        }

        $this->commit();

        return $this->findOne($userId);
    }
}
