<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Helper\Token as TokenHelper;
use App\Command\Token\Exchange;
use App\Repository\UserInterface;
use Interop\Container\ContainerInterface;

/**
 * Handles Token commands.
 */
class Token implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $userRepository;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Token(
                $container
                    ->get('repositoryFactory')
                    ->create('User')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\UserInterface $userRepository
     *
     * @return void
     */
    public function __construct(
        UserInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Creates a new attribute data in the given user.
     *
     * @param App\Command\Token\Exchange $command
     *
     * @return string
     */
    public function handleExchange(Exchange $command) : string {

        $companyToken  = null;
        $user          = $command->user;
        $actingCompany = $command->actingCompany;
        $targetCompany = $command->targetCompany;
        $credential    = $command->credential;

        $relatedUsers    = $this->userRepository->findAllRelatedToCompany($user, $targetCompany);
        $highestRoleUser = $relatedUsers->first();

        $companyToken = TokenHelper::generateCompanyToken(implode(':', [$highestRoleUser->public, $highestRoleUser->username]), $targetCompany->public_key, $targetCompany->private_key);

        return $companyToken;
    }

}
