<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Feature;

use App\Entity\Profile\Feature;
use App\Event\Profile\Feature\Created;
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
