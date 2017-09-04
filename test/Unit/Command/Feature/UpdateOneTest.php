<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command\Feature;

use App\Command\Profile\Feature\UpdateOne;
use Test\Unit\AbstractUnit;

class UpdateOneTest extends AbstractUnit {
    public function testSetParameters() {
        $command = new UpdateOne();
        $this->assertNull($command->value);
        $this->assertNull($command->featureSlug);
        $this->assertNull($command->userId);

        $this->assertInstanceOf(
            UpdateOne::class,
            $command->setParameters([])
        );
        $this->assertNull($command->value);
        $this->assertNull($command->featureSlug);
        $this->assertNull($command->userId);

        $value       = 'value';
        $featureSlug = 'feature-slug';
        $userId      = 1;

        $command->setParameters(['value' => $value]);
        $this->assertSame($value, $command->value);
        $this->assertNull($command->userId);
        $this->assertNull($command->featureSlug);

        $command->setParameters(['featureSlug' => $featureSlug]);
        $this->assertSame($featureSlug, $command->featureSlug);
        $this->assertNull($command->userId);

        $command->setParameters(['userId' => $userId]);
        $this->assertSame($userId, $command->userId);
    }
}
