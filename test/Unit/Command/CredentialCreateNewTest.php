<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command;

use App\Command\CredentialCreateNew;

class CredentialCreateNewTest extends \PHPUnit_Framework_TestCase {

    public function testSetParameters() {
        $command = new CredentialCreateNew();
        $this->assertNull($command->name);
        $this->assertFalse($command->production);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            CredentialCreateNew::class,
            $command->setParameters([])
        );
        $this->assertNull($command->name);
        $this->assertFalse($command->production);
        $this->assertNull($command->companyId);

        $command->setParameters(['name' => 'a']);
        $this->assertSame('a', $command->name);
        $this->assertFalse($command->production);
        $this->assertNull($command->companyId);

        $command->setParameters(['production' => true]);
        $this->assertSame('a', $command->name);
        $this->assertTrue($command->production);
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame('a', $command->name);
        $this->assertTrue($command->production);
        $this->assertSame(1, $command->companyId);
    }
}
