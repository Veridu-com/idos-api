<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Tag;

use App\Entity\Tag;
use App\Event\Tag\DeletedMulti;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedMultiTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tags = [];
        for($i = 0; $i < 5; $i++)
            $tags[] = new Tag([], $optimus);

        $collection = new Collection($tags);

        $deleted = new DeletedMulti($collection);

        $this->assertSame($collection, $deleted->tags);
    }
}
