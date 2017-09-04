<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Service as ServiceEntity;
use App\Factory\Entity;
use App\Repository\DBHandler;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBHandlersTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetAll() {
        $array = [
            'id'         => null,
            'name'       => 'New Service',
            'url'        => 'url',
            'access'     => 0x01,
            'enabled'    => true,
            'public'     => 'publicKey',
            'listens'    => ['listen1', 'listen2'],
            'triggers'   => ['trigger1', 'trigger2'],
            'created_at' => time(),
            'updated_at' => time()
        ];

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $queryMock
            ->method('get')
            ->willReturn(
                new Collection(
                    [
                        new ServiceEntity($array, $this->optimus)
                    ]
                )
            );

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

        $dbService = new DBHandler(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $dbService->getAll()->first()->toArray());
    }

    public function testGetAllEmpty() {
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection()));

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

        $dbService = new DBHandler(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->assertEmpty($dbService->getAll()->toArray());
    }
}
