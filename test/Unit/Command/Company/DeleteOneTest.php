<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Company;

use App\Command\Company\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->company);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->company);

        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command->setParameters(['company' => $entityMock]);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($entityMock, $command->company);
    }
}
