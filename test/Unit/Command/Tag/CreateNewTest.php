<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Tag;

use App\Command\Tag\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->targetUser);
        $this->assertNull($command->name);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );

        $this->assertNull($command->targetUser);
        $this->assertNull($command->name);

        $command->setParameters(['targetUser' => 'a']);
        $this->assertSame('a', $command->targetUser);
        $this->assertNull($command->name);

        $command->setParameters(['name' => 'b']);
        $this->assertSame('a', $command->targetUser);
        $this->assertSame('b', $command->name);
    }
}
