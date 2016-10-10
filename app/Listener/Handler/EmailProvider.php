<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Handler;

use App\Event\Company\Member\InvitationCreated;
use App\Listener\AbstractListenerProvider;
use App\Listener\Handler\Email\SendsInvitation;
use Interop\Container\ContainerInterface;

class EmailProvider extends AbstractListenerProvider {
    public function __construct(ContainerInterface $container) {
        $repositoryFactory        = $container->get('repositoryFactory');
        $credentialRepository     = $repositoryFactory->create('Company\Credential');
        $settingRepository        = $repositoryFactory->create('Company\Setting');
        $serviceHandlerRepository = $repositoryFactory->create('ServiceHandler');

        $gearmanClient = $container->get('gearmanClient');

        $this->events = [
            InvitationCreated::class => [
                new SendsInvitation($gearmanClient)
            ],

        ];
    }
}
