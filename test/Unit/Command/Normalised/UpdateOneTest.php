<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Normalised;

use App\Command\Normalised\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->user);
        $this->assertNull($command->sourceId);
        $this->assertNull($command->name);
        $this->assertNull($command->value);

        $this->assertInstanceOf(
            UpdateOne::class,
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
