<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Member;

use App\Command\Member\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->userId);
        $this->assertNull($command->role);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );

        $this->assertNull($command->userId);
        $this->assertNull($command->role);
        $this->assertNull($command->companyId);

        $command->setParameters(['userId' => 1]);
        $this->assertSame(1, $command->userId);
        $this->assertNull($command->companyId);
        $this->assertNull($command->role);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->userId);
        $this->assertSame(1, $command->companyId);

        $command->setParameters(['role' => 'admin']);
        $this->assertSame(1, $command->userId);
        $this->assertSame('admin', $command->role);
        $this->assertSame(1, $command->companyId);
    }
}
