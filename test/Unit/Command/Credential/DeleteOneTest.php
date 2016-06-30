<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Command\Credential;

use App\Command\Credential\DeleteOne;

class DeleteOneTest extends \PHPUnit_Framework_TestCase {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->credentialId);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->credentialId);

        $command->setParameters(['credentialId' => 1]);
        $this->assertSame(1, $command->credentialId);
    }
}
