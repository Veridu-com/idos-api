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
use App\Entity\ServiceHandler;
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
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCompany($entity, string $name = 'company') : void {
        Validator::instance(Company::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid identity entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertIdentity($entity, string $name = 'identity') : void {
        Validator::instance(Identity::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid credential entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCredential($entity, string $name = 'credential') : void {
        Validator::instance(Credential::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid hook entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertHook($entity, string $name = 'hook') : void {
        Validator::instance(Hook::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid source entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSource($entity, string $name = 'source') : void {
        Validator::instance(Source::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid user entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUser($entity, string $name = 'user') : void {
        Validator::instance(User::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid service entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertService($entity, string $name = 'service') : void {
        Validator::instance(Service::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid handler entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertHandler($entity, string $name = 'handler') : void {
        Validator::instance(Handler::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid Service Handler entity.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertServiceHandler($entity, string $name = 'serviceHandler') : void {
        Validator::instance(ServiceHandler::class)
            ->setName($name)
            ->assert($entity);
    }

    /**
     * Asserts a valid Event.
     *
     * @param mixed  $entity
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertEvent($entity, string $name = 'event') : void {
        Validator::instance(EventInterface::class)
            ->setName($name)
            ->assert($entity);
    }
}
