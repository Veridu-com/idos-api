<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Setting;

use App\Command\Company\Setting\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['section' => 'section']);
        $this->assertSame('section', $command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['property' => 'property']);
        $this->assertSame('property', $command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['value' => 'value']);
        $this->assertSame('value', $command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 'companyId']);
        $this->assertSame('companyId', $command->companyId);
    }
}
