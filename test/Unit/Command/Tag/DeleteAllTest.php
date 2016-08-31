<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Tag;

use App\Command\Tag\DeleteAll;
use Test\Unit\AbstractUnit;

class DeleteAllTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteAll();
        $this->assertNull($command->user);

        $this->assertInstanceOf(
            DeleteAll::class,
            $command->setParameters([])
        );
        $this->assertNull($command->user);

        $command->setParameters(['user' => 'a']);
        $this->assertSame('a', $command->user);
    }
}
