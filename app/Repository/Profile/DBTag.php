<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Profile;

use App\Entity\Profile\Tag;
use App\Repository\AbstractDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Tag Repository Implementation.
 */
class DBTag extends AbstractDBRepository implements TagInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'tags';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Profile\Tag';
    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'slug' => 'string'
    ];

    /**
     * {@inheritdoc}
     */
    public function findOne(string $slug, int $userId) : Tag {
        return $this->findOneBy(
            [
            'user_id' => $userId,
            'slug'    => $slug
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getByUserId(int $userId, array $queryParams = []) : Collection {
        return $this->findBy(['user_id' => $userId], $queryParams);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUserId(int $userId) : int {
        return $this->deleteBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneByUserIdAndSlug(int $userId, string $name) : int {
        return $this->deleteBy(['user_id' => $userId, 'slug' => $name]);
    }
}
