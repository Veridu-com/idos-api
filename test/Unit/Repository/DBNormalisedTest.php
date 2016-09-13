<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Normalised as NormalisedEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBNormalised;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBNormalisedTest extends AbstractUnit {
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

        return new NormalisedEntity(
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

        $dbNormalised = new DBNormalised(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($collection, $dbNormalised->getAllByUserIdAndSourceId(0, 0));
    }

    public function testGetAllByUserIdSourceIdAndNames() {
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

        $dbNormalised = new DBNormalised(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($collection, $dbNormalised->getAllByUserIdSourceIdAndNames(0, 0, ['mapped-1', 'mapped-2']));
    }

    public function testFindOneByUserIdSourceIdAndName() {
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

        $dbNormalised = new DBNormalised(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($entity, $dbNormalised->findOneByUserIdSourceIdAndName(1, 1, 'mapped-1'));
    }

    public function testFindOneByUserIdSourceIdAndNameNotFound() {
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

        $dbNormalised = new DBNormalised(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->setExpectedException(NotFound::class);
        $dbNormalised->findOneByUserIdSourceIdAndName(1, 1, 'mapped-1');
    }
}
