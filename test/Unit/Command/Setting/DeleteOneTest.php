<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Setting;

use App\Command\Setting\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );

        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->companyId);

        $command->setParameters(['section' => 'section']);
        $this->assertSame('section', $command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->companyId);

        $command->setParameters(['property' => 'property']);
        $this->assertSame('property', $command->property);
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
    }
}
