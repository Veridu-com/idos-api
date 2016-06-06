<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command;

use App\Command\CompanyDeleteOne;

class CompanyDeleteOneTest extends \PHPUnit_Framework_TestCase {

    public function testSetParameters() {
        $command = new CompanyDeleteOne();
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            CompanyDeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
    }
}
