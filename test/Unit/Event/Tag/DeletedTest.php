<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Tag;

use App\Entity\Profile\Tag;
use App\Event\Profile\Tag\Deleted;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tag = new Tag([], $optimus);

        $deleted = new Deleted($tag);

        $this->assertSame($tag, $deleted->tag);
    }
}
