<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add entity assertion.
 */
trait AssertEntity {
    /**
     * Asserts a valid company entity.
     *
     * @param mixed $company
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCompany($company) {
        Validator::instance('App\\Entity\\Company')
            ->assert($company);
    }

    /**
     * Asserts a valid identity entity.
     *
     * @param mixed $identity
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertIdentity($identity) {
        Validator::instance('App\\Entity\\Identity')
            ->assert($identity);
    }

    /**
     * Asserts a valid credential entity.
     *
     * @param mixed $credential
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCredential($credential) {
        Validator::instance('App\\Entity\\Company\\Credential')
            ->assert($credential);
    }

    /**
     * Asserts a valid hook entity.
     *
     * @param mixed $hook
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertHook($hook) {
        Validator::instance('App\\Entity\\Company\\Hook')
            ->assert($hook);
    }

    /**
     * Asserts a valid source entity.
     *
     * @param mixed $source
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSource($source) {
        Validator::instance('App\\Entity\\Profile\\Source')
            ->assert($source);
    }

    /**
     * Asserts a valid user entity.
     *
     * @param mixed $user
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUser($user) {
        Validator::instance('App\\Entity\\User')
            ->assert($user);
    }

    /**
     * Asserts a valid service entity.
     *
     * @param mixed $service
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertService($service) {
        Validator::instance('App\\Entity\\Service')
            ->assert($service);
    }

    /**
     * Asserts a valid handler entity.
     *
     * @param mixed $handler
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertHandler($handler) {
        Validator::instance('App\\Entity\\Handler')
            ->assert($handler);
    }

    /**
     * Asserts a valid Service Handler entity.
     *
     * @param mixed $entity
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertServiceHandler($entity) {
        Validator::instance('App\\Entity\\ServiceHandler')
            ->assert($entity);
    }

    /**
     * Asserts a valid Event.
     *
     * @param mixed $entity
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertEvent($entity) {
        Validator::instance('\\League\\Event\\EventInterface')
            ->assert($entity);
    }
}
