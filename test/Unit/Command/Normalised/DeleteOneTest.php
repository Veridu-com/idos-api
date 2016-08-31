<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Normalised;

use App\Command\Normalised\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);

        $command->setParameters(['user' => 'a']);
        $this->assertSame('a', $command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);

        $command->setParameters(['sourceId' => 1]);
        $this->assertSame('a', $command->user);
        $this->assertSame(1, $command->sourceId);
        $this->assertNull($command->name);

        $command->setParameters(['name' => 'b']);
        $this->assertSame('a', $command->user);
        $this->assertSame(1, $command->sourceId);
        $this->assertSame('b', $command->name);
    }
}
