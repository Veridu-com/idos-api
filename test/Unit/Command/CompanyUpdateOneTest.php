<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command;

use App\Command\CompanyUpdateOne;

class CompanyUpdateOneTest extends \PHPUnit_Framework_TestCase {

    public function testSetParameters() {
        $command = new CompanyUpdateOne();
        $this->assertNull($command->newName);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            CompanyUpdateOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->newName);
        $this->assertNull($command->companyId);

        $command->setParameters(['newName' => 'a']);
        $this->assertSame('a', $command->newName);
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame('a', $command->newName);
        $this->assertSame(1, $command->companyId);
    }
}
