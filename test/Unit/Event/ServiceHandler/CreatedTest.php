<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\ServiceHandler;

use App\Entity\ServiceHandler;
use App\Event\ServiceHandler\Created;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class CreatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceHandler = new ServiceHandler([], $optimus);

        $created = new Created($serviceHandler);

        $this->assertInstanceOf(ServiceHandler::class, $created->serviceHandler);
    }
}