<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Credential;

use App\Entity\Credential;
use App\Event\Credential\DeletedMulti;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedMultiTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $credentials = [];
        for($i = 0; $i < 5; $i++)
            $credentials[] = new Credential([], $optimus);

        $collection = new Collection($credentials);

        $deleted = new DeletedMulti($collection);

        $this->assertSame($collection, $deleted->credentials);
    }
}
