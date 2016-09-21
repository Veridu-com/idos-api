<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Member;

use App\Entity\Company\Member;
use App\Event\Company\Member\Deleted;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $member = new Member([], $optimus);

        $deleted = new Deleted($member);

        $this->assertSame($member, $deleted->member);
    }
}
