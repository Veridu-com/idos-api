<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Feature;

use App\Command\Feature\CreateNew;
use Test\Unit\AbstractUnit;

class CreateNewTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new CreateNew();
        $this->assertNull($command->name);
        $this->assertNull($command->value);
        $this->assertNull($command->userId);

        $this->assertInstanceOf(
            CreateNew::class,
            $command->setParameters([])
        );
        $this->assertNull($command->name);
        $this->assertNull($command->value);
        $this->assertNull($command->userId);

        $name   = 'name';
        $value  = 'value';
        $userId = 1;

        $command->setParameters(['name' => $name]);
        $this->assertSame($name, $command->name);
        $this->assertNull($command->userId);
        $this->assertNull($command->value);

        $command->setParameters(['value' => $value]);
        $this->assertSame($value, $command->value);
        $this->assertNull($command->userId);

        $command->setParameters(['userId' => $userId]);
        $this->assertSame($userId, $command->userId);
    }
}
