<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Tag;

use App\Command\Profile\Tag\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->user);
        $this->assertNull($command->slug);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );

        $this->assertNull($command->user);
        $this->assertNull($command->slug);

        $command->setParameters(['user' => 'a']);
        $this->assertSame('a', $command->user);
        $this->assertNull($command->slug);

        $command->setParameters(['slug' => 'b']);
        $this->assertSame('a', $command->user);
        $this->assertSame('b', $command->slug);
    }
}
