<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Factory;

use App\Factory\Command;

class CommandTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->factory = new Command();
    }

    public function testCorrectInterface() {
        $commands = [
            'CompanyCreateNew',
            'CompanyDeleteAll',
            'CompanyDeleteOne',
            'CompanyUpdateOne'
        ];
        foreach ($commands as $command)
            $this->assertInstanceOf(
                'App\\Command\\CommandInterface',
                $this->factory->create($command)
            );
    }
}
