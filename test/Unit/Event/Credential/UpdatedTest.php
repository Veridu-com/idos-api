<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Event\Credential;

use App\Entity\Credential;
use App\Event\Credential\Updated;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class UpdatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $credential = new Credential([], $optimus);

        $updated = new Updated($credential);

        $this->assertInstanceOf(Credential::class, $updated->credential);
    }
}