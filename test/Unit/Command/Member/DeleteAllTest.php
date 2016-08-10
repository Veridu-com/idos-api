<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Member;

use App\Command\Member\DeleteAll;
use Test\Unit\AbstractUnit;

class DeleteAllTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->credential);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->credential);

        $command->setParameters(['credential' => 'pubKey']);
        $this->assertSame('pubKey', $command->credential);
    }
}
