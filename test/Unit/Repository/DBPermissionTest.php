<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Company\Permission;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBPermission;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBPermissionTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindOneNotFound() {
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
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

        $dbPermission = new DBPermission(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->setExpectedException(NotFound::class);
        $dbPermission->findOne(1, 'notAExistingRoutName');
    }

    public function testFindOne() {
        $array = [
            'route_name' => 'companies:listAll',
            'created_at' => time()
        ];

        $factory = new Entity($this->optimus);
        $entity  = $factory->create('Permission', $array);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
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
        $dbPermission = new DBPermission($factory, $this->optimus, $connectionMock);

        // fetches entity
        $entity = $dbPermission->findOne(0, 'companies:listAll');

        $this->assertInstanceOf(Permission::class, $entity);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $entity->toArray());
    }
}
