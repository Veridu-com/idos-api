<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\ServiceHandler;

use App\Command\Service\DeleteAll;
use Test\Unit\AbstractUnit;

class DeleteAllTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
    }
}
