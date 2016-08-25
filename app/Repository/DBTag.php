<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Tag;
use App\Exception\NotFound;
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

        if(! empty($tags)) {
            $result = $result->whereIn('slug', $tags);
        }

        $result = $result->get();

        return new Collection($result);
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
    public function findOneByUserIdAndSlug(int $userId, string $name) : Tag {
        $result = $this->findBy(['user_id' => $userId, 'slug' => $name]);

        if($result->isEmpty()) {
            throw new NotFound();
        }

        return $result->first();

    }

    /**
     * {@inheritdoc}
     */
    public function deleteOneByUserIdAndSlug(int $userId, string $name) : int {
        return $this->deleteBy(['user_id' => $userId, 'slug' => $name]);
    }
}
