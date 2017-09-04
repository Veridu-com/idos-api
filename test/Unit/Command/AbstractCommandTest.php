<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Command;

use App\Command\AbstractCommand;
use Test\Unit\AbstractUnit;

/**
 * Abstract Command Implementation.
 */
class AbstractCommandTest extends AbstractUnit {
    public function testSetParameter() {
        $abstractMock = $this->getMockBuilder(AbstractCommand::class)
            ->getMockForAbstractClass();
        $property = $this->setProtectedMethod($abstractMock, 'setParameter');
        $this->expectedException(\RuntimeException::class);
        $property->invoke($abstractMock, 'prop', 'value');
    }

    /**
     * Given the associative array, this function test public and private properties.
     *
     * @example [ 'name' => 'public', 'companyId' => 'private' ]
     *
     * Where "public" keys can be assign by the Command@setParameters method.
     * Where "private" kes can't be assigned by the Command@setParameters method.
     *
     * @param array $parameters The parameters
     */
    public function assertSetParameters(string $commandClassName, array $parameters) {
        foreach ($parameters as $parameter => $config) {
            $command = new $commandClassName();

            if ($config['policy'] == 'public') {
                $command->setParameters([$parameter => 1]);

                foreach ($parameters as $_parameter => $_config) {
                    $prop = $_config['property'];
                    if ($_parameter == $parameter) {
                        $this->assertSame(1, $command->$prop);
                    } else {
                        $this->assertNull($command->$prop);
                    }
                }
            } else {
                $command->setParameters([$parameter => 1]);

                foreach ($parameters as $_parameter => $_config) {
                    $prop = $_config['property'];
                    $this->assertNull($command->$prop);
                }
            }
        }
    }
}
