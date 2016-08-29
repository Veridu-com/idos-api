<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Permission;

use App\Command\Permission\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->routeName);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );
        $this->assertNull($command->routeName);
        $this->assertNull($command->companyId);

        $command->setParameters(['routeName' => 'a']);
        $this->assertSame('a', $command->routeName);
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame('a', $command->routeName);
        $this->assertSame(1, $command->companyId);
    }
}
