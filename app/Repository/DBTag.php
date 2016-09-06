<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Tag;
use Illuminate\Support\Collection;

/**
 * Database-based Tag Repository Implementation.
 */
class DBTag extends AbstractSQLDBRepository implements TagInterface {
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
    protected $entityName = 'Tag';

    /**
     * {@inheritdoc}
     */
    public function getAllByUserId(int $userId) : Collection {
        return $this->findBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByUserIdAndTagSlugs(int $userId, array $tags) : Collection {
        $result = $this->query()
            ->selectRaw('tags.*')
            ->where('user_id', '=', $userId);

        if (! empty($tags)) {
            $result = $result->whereIn('slug', $tags);
        }

        return $result->get();
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
    public function findOneByUserIdAndSlug(int $userId, string $slug) : Tag {
        return $this->findOneBy(['user_id' => $userId, 'slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneByUserIdAndSlug(int $userId, string $name) : int {
        return $this->deleteBy(['user_id' => $userId, 'slug' => $name]);
    }
}
