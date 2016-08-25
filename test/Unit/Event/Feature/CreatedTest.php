<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Feature;

use App\Entity\Feature;
use App\Event\Feature\Created;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class CreatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature = new Feature([], $optimus);

        $created = new Created($feature);

        $this->assertInstanceOf(Feature::class, $created->feature);
    }
}
