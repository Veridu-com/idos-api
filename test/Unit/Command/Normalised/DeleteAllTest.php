<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Normalised;

use App\Command\Normalised\DeleteAll;
use Test\Unit\AbstractUnit;

class DeleteAllTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);

        $command->setParameters(['user' => 'a']);
        $this->assertSame('a', $command->user);
        $this->assertNull($command->sourceId);

        $command->setParameters(['sourceId' => 1]);
        $this->assertSame('a', $command->user);
        $this->assertSame(1, $command->sourceId);
    }
}
