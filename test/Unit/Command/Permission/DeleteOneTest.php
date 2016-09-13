<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Permission;

use App\Command\Permission\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->companyId);
        $this->assertNull($command->routeName);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
        $this->assertNull($command->routeName);

        $command->setParameters(['routeName' => 1]);
        $this->assertSame(1, $command->routeName);
    }
}
