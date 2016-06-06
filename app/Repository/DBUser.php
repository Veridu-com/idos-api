<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Database-based User Repository Implementation.
 */
class DBUser extends AbstractDBRepository implements UserInterface {
    /**
     * Class constructor.
     *
     * @param App\Model\User $model
     *
     * @return void
     */
    public function __construct(User $model) {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findByUserName($userName, $credentialId) {
        try {
            return $this->model
                ->where('username', $userName)
                ->where('credential_id', $credentialId)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }
}
