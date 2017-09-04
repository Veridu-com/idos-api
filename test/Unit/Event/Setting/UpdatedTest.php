<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Setting;

use App\Entity\Company\Setting;
use App\Event\Company\Setting\Updated;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class UpdatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $setting = new Setting([], $optimus);

        $updated = new Updated($setting);

        $this->assertInstanceOf(Setting::class, $updated->setting);
    }
}
