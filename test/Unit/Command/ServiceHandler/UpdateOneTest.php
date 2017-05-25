<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\ServiceHandler;

use App\Command\Service\UpdateOne;
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
