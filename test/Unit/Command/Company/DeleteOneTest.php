<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command;

use App\Command\Company\DeleteOne;

class DeleteOneTest extends \PHPUnit_Framework_TestCase {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
    }
}
