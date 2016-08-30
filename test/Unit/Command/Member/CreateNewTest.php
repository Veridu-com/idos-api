<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Member;

use App\Command\Member\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->userName);
        $this->assertNull($command->role);
        $this->assertNull($command->credential);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );

        $this->assertNull($command->userName);
        $this->assertNull($command->role);
        $this->assertNull($command->credential);

        $command->setParameters(['userName' => 'a']);
        $this->assertSame('a', $command->userName);
        $this->assertNull($command->credential);
        $this->assertNull($command->role);

        $command->setParameters(['credential' => 'pubKey']);
        $this->assertSame('a', $command->userName);
        $this->assertSame('pubKey', $command->credential);

        $command->setParameters(['role' => 'admin']);
        $this->assertSame('a', $command->userName);
        $this->assertSame('admin', $command->role);
        $this->assertSame('pubKey', $command->credential);
    }
}
