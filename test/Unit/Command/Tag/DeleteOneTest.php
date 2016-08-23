<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Tag;

use App\Command\Tag\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->targetUser);
        $this->assertNull($command->name);

        $this->assertInstanceOf(
            DeleteOne::class,
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
