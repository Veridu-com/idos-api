<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\ServiceHandler;

use App\Command\ServiceHandler\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->slug);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->slug);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);

        $command->setParameters(['slug' => 'slug']);
        $this->assertSame('slug', $command->slug);
        $this->assertNull($command->companyId);
        $this->assertNull($command->serviceSlug);

        $command->setParameters(['companyId' => 1]);
        $this->assertSame(1, $command->companyId);
        $this->assertSame('slug', $command->slug);
        $this->assertNull($command->serviceSlug);

        $command->setParameters(['service' => 'service']);
        $this->assertSame(1, $command->companyId);
        $this->assertSame('slug', $command->slug);
        $this->assertSame('service', $command->serviceSlug);

    }
}
