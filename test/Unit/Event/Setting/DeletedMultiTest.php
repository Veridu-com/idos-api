<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Setting;

use App\Entity\Setting;
use App\Event\Setting\DeletedMulti;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedMultiTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $settings = [];
        for($i = 0; $i < 5; $i++)
            $settings[] = new Setting([], $optimus);

        $collection = new Collection($settings);

        $deleted = new DeletedMulti($collection);

        $this->assertSame($collection, $deleted->settings);
    }
}
