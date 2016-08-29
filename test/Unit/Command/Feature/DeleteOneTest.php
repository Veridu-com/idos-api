<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command\Feature;

use App\Command\Feature\DeleteOne;
use Test\Unit\AbstractUnit;

class DeleteOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new DeleteOne();
        $this->assertNull($command->featureSlug);
        $this->assertNull($command->userId);

        $this->assertInstanceOf(
            DeleteOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->featureSlug);
        $this->assertNull($command->userId);

        $featureSlug = 'slug';
        $userId      = 1;

        $command->setParameters(['featureSlug' => $featureSlug]);
        $this->assertSame($featureSlug, $command->featureSlug);
        $this->assertNull($command->userId);

        $command->setParameters(['userId' => $userId]);
        $this->assertSame($userId, $command->userId);
    }
}
