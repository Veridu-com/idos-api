<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Tag;

use App\Entity\Profile\Tag;
use App\Event\Profile\Tag\Updated;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class UpdatedTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tag = new Tag([], $optimus);

        $updated = new Updated($tag);

        $this->assertInstanceOf(Tag::class, $updated->tag);
    }
}
