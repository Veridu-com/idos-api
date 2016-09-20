<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Setting;

use App\Entity\Company\Setting;
use App\Event\Company\Setting\Deleted;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $setting = new Setting([], $optimus);

        $deleted = new Deleted($setting);

        $this->assertSame($setting, $deleted->setting);
    }
}
