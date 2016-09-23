<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Credential;

use App\Entity\Company\Credential;
use App\Event\Company\Credential\Created;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class CreatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $credential = new Credential([], $optimus);

        $created = new Created($credential);

        $this->assertInstanceOf(Credential::class, $created->credential);
    }
}
