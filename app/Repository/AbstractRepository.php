<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * Abstract Generic Repository.
 */
abstract class AbstractRepository implements RepositoryInterface {
    /**
     * Model Instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Class constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes) {
        return $this->model->newInstance($attributes);
    }
}
