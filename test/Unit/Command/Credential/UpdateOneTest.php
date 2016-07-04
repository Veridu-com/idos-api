<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Credential;

use App\Command\Credential\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->name);
        $this->assertNull($command->credentialId);

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->name);
        $this->assertNull($command->credentialId);

        $command->setParameters(['name' => 'a']);
        $this->assertSame('a', $command->name);
        $this->assertNull($command->credentialId);

        $command->setParameters(['credentialId' => 1]);
        $this->assertSame('a', $command->name);
        $this->assertSame(1, $command->credentialId);
    }
}
