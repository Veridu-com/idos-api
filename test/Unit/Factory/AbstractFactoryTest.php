<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Factory;

use App\Factory\AbstractFactory;
use App\Factory\Command;
use Test\Unit\AbstractUnit;

class AbstractFactoryTest extends AbstractUnit {
    public function testGetFormatedNameNotString() {
        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $method = $this->setProtectedMethod($abstractMock, 'getFormattedName');

        $this->setExpectedException(\TypeError::class);
        $method->invoke($abstractMock, new \stdClass());

    }

    public function testGetFormatedName() {
        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $method = $this->setProtectedMethod($abstractMock, 'getFormattedName');
        $this->assertSame('Factory', $method->invoke($abstractMock, 'factory'));

    }

    public function testGetClassName() {
        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $method = $this->setProtectedMethod($abstractMock, 'getClassName');
        $this->assertSame('Company', $method->invoke($abstractMock, 'company'));
    }

    public function testRegisterNotFound() {
        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $this->setExpectedException(\RuntimeException::class);
        $abstractMock->register('dummy', 'App\Entity\Dummy');
    }

    public function testRegister() {
        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $this->assertInstanceOf(AbstractFactory::class, $abstractMock->register('Company', 'App\Entity\Company'));
    }

    public function testCreateNotFound() {
        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $this->setExpectedException(\RuntimeException::class);
        $abstractMock->create('dummy');
    }

    public function testCreate() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $abstractMock = $this->getMockBuilder(AbstractFactory::class)
            ->setMethods(['getNamespace'])
            ->getMockForAbstractClass();

        $abstractMock
            ->method('getNamespace')
            ->will($this->returnValue('App\\Factory\\'));

        $this->assertInstanceOf(Command::class, $abstractMock->create('command'));
    }
}
