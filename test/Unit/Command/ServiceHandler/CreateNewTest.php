<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\ServiceHandler;

use App\Command\Service\CreateNew;
use Test\Unit\Command\AbstractCommandTest;

class CreateNewTest extends AbstractCommandTest {
    public function testSetParameters() {
        $attributes = [
            'companyId' => [
                'property' => 'companyId',
                'policy'   => 'private'
            ],
            'decoded_service_id' => [
                'property' => 'handlerId',
                'policy'   => 'public'
            ],
            'listens' => [
                'property' => 'listens',
                'policy'   => 'public'
            ]
        ];

        $this->assertSetParameters(CreateNew::class, $attributes);
    }
}
