<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\ServiceHandler;

use App\Command\ServiceHandler\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->name);
        $this->assertNull($command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );

        $this->assertNull($command->name);
        $this->assertNull($command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['name' => 'a']);
        $this->assertSame('a', $command->name);
        $this->assertNull($command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['source' => 'source']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertNull($command->location);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['location' => 'location']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertSame('location', $command->location);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertEquals('location', $command->location);
        $this->assertEquals(1, $command->companyId);
        $this->assertNull($command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['service' => 'service']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertEquals('location', $command->location);
        $this->assertEquals(1, $command->companyId);
        $this->assertSame('service', $command->serviceSlug);
        $this->assertNull($command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['authPassword' => 'authPassword']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertEquals('location', $command->location);
        $this->assertEquals(1, $command->companyId);
        $this->assertSame('service', $command->serviceSlug);
        $this->assertSame('authPassword', $command->authPassword);
        $this->assertNull($command->authUsername);

        $command->setParameters(['authUsername' => 'authUsername']);
        $this->assertSame('a', $command->name);
        $this->assertSame('source', $command->source);
        $this->assertEquals('location', $command->location);
        $this->assertEquals(1, $command->companyId);
        $this->assertSame('service', $command->serviceSlug);
        $this->assertSame('authPassword', $command->authPassword);
        $this->assertSame('authUsername', $command->authUsername);
    }
}
