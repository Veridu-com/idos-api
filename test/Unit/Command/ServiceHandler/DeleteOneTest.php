<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\ServiceHandler;

use App\Command\ServiceHandler\DeleteOne;
use Test\Unit\Command\AbstractCommandTest;

class DeleteOneTest extends AbstractCommandTest {
    public function testSetParameters() {
        $command = new DeleteOne();

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );

        $attributes = [
            'companyId' => [
                'property' => 'companyId',
                'policy'   => 'private'
            ],
            'serviceHandlerId' => [
                'property' => 'serviceHandlerId',
                'policy'   => 'private'
            ]
        ];

        $this->assertSetParameters(DeleteOne::class, $attributes);

    }
}
