<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Company;

use App\Command\Company\DeleteAll;
use Test\Unit\AbstractUnit;

class DeleteAllTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->parentId);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->parentId);

        $command->setParameters(['parentId' => 1]);
        $this->assertSame(1, $command->parentId);
    }
}
