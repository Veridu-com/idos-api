<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Command;

use App\Command\AbstractCommand;
use Test\Unit\AbstractUnit;

/**
 * Abstract Command Implementation.
 */
class AbstractCommandTest extends AbstractUnit {
    private function setProtectedMethod($object, $method) {
        $reflection        = new \ReflectionClass($object);
        $reflection_method = $reflection->getMethod($method);
        $reflection_method->setAccessible(true);

        return $reflection_method;
    }

    public function testSetParameter() {
        $abstractMock = $this->getMockBuilder(AbstractCommand::class)
            ->getMockForAbstractclass();
        $property = $this->setProtectedMethod($abstractMock, 'setParameter');
        $this->setExpectedException(\RuntimeException::class);
        $property->invoke($abstractMock, 'prop', 'value');
    }
}
