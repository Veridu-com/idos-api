<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Hook;

use App\Entity\Hook;
use App\Event\Hook\DeletedMulti;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedMultiTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hooks = [];
        for($i = 0; $i < 5; $i++)
            $hooks[] = new Hook([], $optimus);

        $collection = new Collection($hooks);

        $created = new DeletedMulti($collection);

        $this->assertSame($collection, $created->hooks);
    }
}
