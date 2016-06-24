<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Exception\NotFound;
use App\Repository\AbstractDBRepository;
use App\Factory\Entity;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Connection\Query\Builder;

class AbstractDBRepositoryTest extends \PHPUnit_Framework_TestCase {

    private function setProtectedProperty($object, $property, $value) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }

    private function setProtectedMethod($object, $method) {
        $reflection = new \ReflectionClass($object);
        $reflection_method = $reflection->getMethod($method);
        $reflection_method->setAccessible(true);
        return $reflection_method;
    }

    public function testGetTableNameRuntimeException() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $getTableName = $this->setProtectedMethod($abstractMock, 'getTableName');
        $this->setExpectedException(\RuntimeException::class);
        $getTableName->invoke($abstractMock);
    }

    public function testGetTableName() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setProtectedProperty($abstractMock, 'tableName', 'AbstractDBRepository');
        $getTableName = $this->setProtectedMethod($abstractMock, 'getTableName');
        $this->assertSame('AbstractDBRepository', $getTableName->invoke($abstractMock));

    }

    public function testGetEntityNameRuntimeException() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $getEntityName = $this->setProtectedMethod($abstractMock, 'getEntityName');
        $this->setExpectedException(\RuntimeException::class);
        $getEntityName->invoke($abstractMock);
    }

    public function testGetEntityName() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setProtectedProperty($abstractMock, 'entityName', 'Entity');
        $getEntityName = $this->setProtectedMethod($abstractMock, 'getEntityName');
        $this->assertSame('Entity', $getEntityName->invoke($abstractMock));
    }

    public function testGetEntityClassNameRuntimeException() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $getEntityClassName = $this->setProtectedMethod($abstractMock, 'getEntityClassName');
        $this->setExpectedException(\RuntimeException::class);
        $getEntityClassName->invoke($abstractMock);
    }

    public function testGetEntityClassName() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntityName'])
            ->getMock();
        $abstractMock
            ->method('getEntityName')
            ->will($this->returnValue('Entity'));

        $getEntityClassName = $this->setProtectedMethod($abstractMock, 'getEntityClassName');
        $this->assertSame('\App\Entity\Entity', $getEntityClassName->invoke($abstractMock));
    }

    public function testFindNotFound() {
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $queryMock
            ->method('find')
            ->will($this->returnValue(""));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->setExpectedException(NotFound::class);
        $abstractMock->find(0);
    }

    public function testFind() {
        $array = [
            'name' => 'AbstractDBCompany',
            'slug' => 'slug',
            'public_key' => 'public_key',
            'created_at' => 'date'
        ];

        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $queryMock
            ->method('find')
            ->will($this->returnValue($array));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->assertSame($array, $abstractMock->find(0));
    }

    public function testFindByKeyNotFound() {
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('first')
            ->will($this->returnValue(""));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->setExpectedException(NotFound::class);
        $findByKey = $this->setProtectedMethod($abstractMock, 'findByKey');
        $findByKey->invoke($abstractMock, 'key', 'value');
    }


    public function testFindByKey() {
        $array = [
            'name' => 'AbstractDBCompany',
            'slug' => 'slug',
            'public_key' => 'public_key',
            'created_at' => 'date'
        ];
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('first')
            ->will($this->returnValue($array));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $findByKey = $this->setProtectedMethod($abstractMock, 'findByKey');
        $this->assertSame($array, $findByKey->invoke($abstractMock, 'key', 'value'));
    }
}
