<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\User;

/**
 * Array-based User Repository Implementation.
 */
class ArrayUser extends AbstractArrayRepository implements UserInterface {
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
        foreach ($this->storage[$credentialId] as $item)
            if ($item->username === $userName)
                return $item;
        throw new NotFound(get_class($this->model));
    }
}
