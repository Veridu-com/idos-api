<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command;

use App\Command\Company\DeleteAll;

class DeleteAllTest extends \PHPUnit_Framework_TestCase {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->parentId);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->parentId);

        $command->setParameters(['parentId' => 1]);
        $this->assertSame(1, $command->parentId);
    }
}
