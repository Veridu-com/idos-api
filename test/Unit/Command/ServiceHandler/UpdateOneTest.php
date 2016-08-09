<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\ServiceHandler;

use App\Command\ServiceHandler\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->name);
        $this->assertNull($command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );

        $this->assertNull($command->name);
        $this->assertNull($command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['name' => 'a']);
        $this->assertSame('a', $command->name);
        $this->assertNull($command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['source' => 'source']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['location' => 'location']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertSame('location', $command->location);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['authPassword' => 'authPassword']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertEquals('location', $command->location);
        $this->assertSame('authPassword', $command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['authUsername' => 'authUsername']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertEquals('location', $command->location);
        $this->assertSame('authPassword', $command->authPassword);
        $this->assertSame('authUsername', $command->authUsername);
    }
}
