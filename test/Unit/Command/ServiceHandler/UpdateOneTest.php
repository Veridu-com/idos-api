<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\ServiceHandler;

use App\Command\ServiceHandler\UpdateOne;
use Test\Unit\Command\AbstractCommandTest;

class UpdateOneTest extends AbstractCommandTest {
    public function testSetParameters() {
        $attributes = [
            'companyId' => [
                'property' => 'companyId',
                'policy'   => 'private'
            ],
            'serviceHandlerId' => [
                'property' => 'serviceHandlerId',
                'policy'   => 'private'
            ],
            'listens' => [
                'property' => 'listens',
                'policy'   => 'public'
            ]
        ];

        $this->assertSetParameters(UpdateOne::class, $attributes);
    }
}
