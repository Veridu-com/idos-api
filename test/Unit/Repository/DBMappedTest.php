<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Mapped as MappedEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBMapped;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBMappedTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getEntity($sourceId, $id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new MappedEntity(
            [
                'id'         => $id,
                'source_id'  => $sourceId,
                'name'       => 'mapped-' . $id,
                'value'      => 'value-' . $id,
                'created_at' => time()
            ],
            $optimus
        );
    }

    public function testGetAllByUserIdAndSourceId() {
        $factory = new Entity($this->optimus);
        $factory->create('Mapped', []);

        $collection = new Collection([$this->getEntity(1, 1), $this->getEntity(1, 2)]);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'join'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue($collection));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));

        $dbMapped = new DBMapped($factory, $this->optimus, $connectionMock);
        $this->assertEquals($collection, $dbMapped->getAllByUserIdAndSourceId(0, 0));
    }

    public function testGetAllByUserIdSourceIdAndNames() {
        $factory = new Entity($this->optimus);
        $factory->create('Mapped', []);

        $collection = new Collection([$this->getEntity(1, 1), $this->getEntity(1, 2)]);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'join', 'whereIn'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('whereIn')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue($collection));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));

        $dbMapped = new DBMapped($factory, $this->optimus, $connectionMock);
        $this->assertEquals($collection, $dbMapped->getAllByUserIdSourceIdAndNames(0, 0, ['mapped-1', 'mapped-2']));
    }

    public function testFindOneByUserIdSourceIdAndName() {
        $factory = new Entity($this->optimus);
        $factory->create('Mapped', []);

        $entity = $this->getEntity(1, 1);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'join'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([$entity])));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));

        $dbMapped = new DBMapped($factory, $this->optimus, $connectionMock);
        $this->assertEquals($entity, $dbMapped->findOneByUserIdSourceIdAndName(1, 1, 'mapped-1'));
    }

    public function testFindOneByUserIdSourceIdAndNameNotFound() {
        $factory = new Entity($this->optimus);
        $factory->create('Mapped', []);

        $entity = $this->getEntity(1, 1);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'join'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([])));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));

        $dbMapped = new DBMapped($factory, $this->optimus, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbMapped->findOneByUserIdSourceIdAndName(1, 1, 'mapped-1');
    }

}
