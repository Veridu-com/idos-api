<?php

namespace Test\Command;

use App\Command\AbstractCommand;

class AbstractCommandTest extends \PHPUnit_Framework_TestCase {

    public function testSetParameter() {
        $abstractMock = $this->getMockBuilder(AbstractCommand::class)
            ->getMockForAbstractClass();
        $abstractMock->prop = null;
        $this->assertInstanceOf(
            AbstractCommand::class,
            $abstractMock->setParameter('prop', 'a')
        );
        $this->assertSame('a', $abstractMock->prop);
    }

    public function testSetParameterInvalidName() {
        $abstractMock = $this->getMockBuilder(AbstractCommand::class)
            ->getMockForAbstractClass();

        $this->setExpectedException(\RuntimeException::class);
        $abstractMock->setParameter('prop', 'a');
    }
}
