<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Event\Permission;

use App\Entity\Company\Permission;
use App\Event\Company\Permission\DeletedMulti;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DeletedMultiTest extends AbstractUnit {
    public function testConstruct() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $permissions = [];
        for ($i = 0; $i < 5; $i++) {
            $permissions[] = new Permission([], $optimus);
        }

        $collection = new Collection($permissions);

        $deleted = new DeletedMulti($collection);

        $this->assertSame($collection, $deleted->permissions);
    }
}
