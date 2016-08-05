<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Member;

use App\Command\Member\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->companyId);
        $this->assertNull($command->userId);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->companyId);
        $this->assertNull($command->userId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
        $this->assertNull($command->userId);

        $command->setParameters(['userId' => 1]);
        $this->assertSame(1, $command->companyId);
        $this->assertSame(1, $command->userId);
    }
}
