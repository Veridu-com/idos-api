<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Digested;

use App\Command\Digested\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);
        $this->assertNull($command->value);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);
        $this->assertNull($command->value);

        $command->setParameters(['user' => 'a']);
        $this->assertSame('a', $command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);
        $this->assertNull($command->value);

        $command->setParameters(['sourceId' => 1]);
        $this->assertSame('a', $command->user);
        $this->assertSame(1, $command->sourceId);
        $this->assertNull($command->name);
        $this->assertNull($command->value);

        $command->setParameters(['name' => 'b']);
        $this->assertSame('a', $command->user);
        $this->assertSame(1, $command->sourceId);
        $this->assertSame('b', $command->name);
        $this->assertNull($command->value);

        $command->setParameters(['value' => 'c']);
        $this->assertSame('a', $command->user);
        $this->assertSame(1, $command->sourceId);
        $this->assertSame('b', $command->name);
        $this->assertSame('c', $command->value);
    }
}
