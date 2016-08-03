<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Member;

use App\Command\Member\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->userName);
        $this->assertNull($command->role);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );

        $this->assertNull($command->userName);
        $this->assertNull($command->role);
        $this->assertNull($command->companyId);

        $command->setParameters(['userName' => 'a']);
        $this->assertSame('a', $command->userName);
        $this->assertNull($command->companyId);
        $this->assertNull($command->role);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame('a', $command->userName);
        $this->assertSame(1, $command->companyId);

        $command->setParameters(['role' => 'admin']);
        $this->assertSame('a', $command->userName);
        $this->assertSame('admin', $command->role);
        $this->assertSame(1, $command->companyId);
    }
}
