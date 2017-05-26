<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use App\Entity\Company;
use App\Entity\Company\Credential;
use App\Entity\Company\Hook;
use App\Entity\Handler;
use App\Entity\Identity;
use App\Entity\Profile\Source;
use App\Entity\Service;
use App\Entity\User;
use League\Event\EventInterface;
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
    public function assertCompany($company) : void {
        Validator::instance(Company::class)
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
    public function assertIdentity($identity) : void {
        Validator::instance(Identity::class)
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
    public function assertCredential($credential) : void {
        Validator::instance(Credential::class)
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
    public function assertHook($hook) : void {
        Validator::instance(Hook::class)
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
    public function assertSource($source) : void {
        Validator::instance(Source::class)
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
    public function assertUser($user) : void {
        Validator::instance(User::class)
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
    public function assertService($service) : void {
        Validator::instance(Service::class)
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
    public function assertHandler($handler) : void {
        Validator::instance(Handler::class)
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
    public function assertServiceHandler($entity) : void {
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
    public function assertEvent($entity) : void {
        Validator::instance(EventInterface::class)
            ->assert($entity);
    }
}
