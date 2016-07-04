<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Factory;

use App\Factory\Command;
use Test\Unit\AbstractUnit;

class CommandTest extends AbstractUnit {
    public function setUp() {
        $this->factory = new Command();
    }

    public function testCorrectInterface() {
        $commands = [
            'Company\CreateNew',
            'Company\DeleteAll',
            'Company\DeleteOne',
            'Company\UpdateOne'
        ];
        foreach ($commands as $command)
            $this->assertInstanceOf(
                'App\\Command\\CommandInterface',
                $this->factory->create($command)
            );
    }
}
