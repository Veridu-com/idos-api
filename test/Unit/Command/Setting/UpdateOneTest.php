<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Setting;

use App\Command\Setting\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->sectionNameId);
        $this->assertNull($command->propNameId);
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );

        $this->assertNull($command->sectionNameId);
        $this->assertNull($command->propNameId);
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['sectionNameId' => 1]);
        $this->assertEquals(1, $command->sectionNameId);
        $this->assertNull($command->propNameId);
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['propNameId' => 1]);
        $this->assertEquals(1, $command->propNameId);
        $this->assertNull($command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['section' => 'section']);
        $this->assertEquals('section', $command->section);
        $this->assertNull($command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['property' => 'property']);
        $this->assertEquals('property', $command->property);
        $this->assertNull($command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['value' => 'value']);
        $this->assertEquals('value', $command->value);
        $this->assertNull($command->companyId);

        $command->setParameters(['companyId' => 1]);
        $this->assertEquals(1, $command->companyId);
    }
}
