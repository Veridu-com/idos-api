<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Entity\User;

/**
 * Array-based User Repository Implementation.
 */
class ArrayUser extends AbstractArrayRepository implements UserInterface {
    /**
     * Class constructor.
     *
     * @param App\Entity\User $entity
     *
     * @return void
     */
    public function __construct(User $entity) {
        $this->entity = $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function findByUserName($userName, $credentialId) {
        foreach ($this->storage[$credentialId] as $item)
            if ($item->username === $userName)
                return $item;
        throw new NotFound(get_class($this->entity));
    }
}
