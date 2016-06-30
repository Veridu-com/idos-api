<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command\Credential;

use App\Command\Credential\DeleteAll;

class DeleteAllTest extends \PHPUnit_Framework_TestCase {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
    }
}
