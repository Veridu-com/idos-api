<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\ServiceHandler;

use App\Entity\ServiceHandler;
use App\Event\Service\Deleted;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceHandler = new ServiceHandler([], $optimus);

        $deleted = new Deleted($serviceHandler);

        $this->assertSame($serviceHandler, $deleted->serviceHandler);
    }
}
