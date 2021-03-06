<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Hook;

use App\Entity\Company\Hook;
use App\Event\Company\Hook\Deleted;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hook = new Hook([], $optimus);

        $deleted = new Deleted($hook);

        $this->assertSame($hook, $deleted->hook);
    }
}
