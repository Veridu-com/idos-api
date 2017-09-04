<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Member;

use App\Entity\Company\Member;
use App\Event\Company\Member\Created;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class CreatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $member = new Member([], $optimus);

        $created = new Created($member);

        $this->assertInstanceOf(Member::class, $created->member);
    }
}
