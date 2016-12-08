<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Setting;

use App\Command\Company\Setting\UpdateOne;
use Test\Unit\Command\AbstractCommandTest;

class UpdateOneTest extends AbstractCommandTest {
    public function testSetParameters() {
        $command = new UpdateOne();

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );

        $attributes = [
            'settingId' => [
                'property' => 'settingId',
                'policy'   => 'private'
            ],
            'value' => [
                'property' => 'value',
                'policy'   => 'public'
            ]
        ];

        $this->assertSetParameters(UpdateOne::class, $attributes);
    }
}
