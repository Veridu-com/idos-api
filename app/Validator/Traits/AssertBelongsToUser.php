<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use App\Entity\User;
use Respect\Validation\Exceptions\AllOfException;

/**
 * Trait to check if an entity has userId == $user->id assertion.
 */
trait AssertBelongsToUser {
    /**
     * Asserts that an entity belongs to an User.
     *
     * @param mixed           $entity
     * @param App\Entity\User $user
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertBelongsToUser($entity, User $user) {
        if ($entity->userId !== $user->id) {
            throw new AllOfException('Failed asserting that entity belongs to user.');
        }
    }
}
