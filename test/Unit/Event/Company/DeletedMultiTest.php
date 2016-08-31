<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Company;

use App\Entity\Company;
use App\Event\Company\DeletedMulti;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedMultiTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $companies = [];
        for($i = 0; $i < 5; $i++)
            $companies[] = new Company([], $optimus);

        $collection = new Collection($companies);

        $deleted = new DeletedMulti($collection);

        $this->assertSame($collection, $deleted->companies);
    }
}
