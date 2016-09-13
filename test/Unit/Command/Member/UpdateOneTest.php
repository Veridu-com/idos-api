<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Member;

use App\Command\Member\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->memberId);
        $this->assertNull($command->role);

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );

        $this->assertNull($command->memberId);
        $this->assertNull($command->role);

        $command->setParameters(['memberId' => 1]);
        $this->assertSame(1, $command->memberId);
        $this->assertNull($command->role);

        $command->setParameters(['role' => 'admin']);
        $this->assertSame(1, $command->memberId);
        $this->assertSame('admin', $command->role);
    }
}
