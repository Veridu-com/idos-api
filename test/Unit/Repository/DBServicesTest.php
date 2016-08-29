<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Service as ServiceEntity;
use App\Factory\Entity;
use App\Repository\DBService;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBServiceTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @group failing
     */
    public function testGetAll() {
         $array = [
            'id'         => null,
            'name'       => 'New Service',
            'url'        => 'url',
            'access'     => 0x01,
            'enabled'    => true,
            'listens'    => ['listen1', 'listen2'],
            'triggers'   => ['trigger1', 'trigger2'],
            'enabled'    => true,
            'created_at' => time(),
            'updated_at' => time()
         ];

         $factory = new Entity($this->optimus);
         $factory->create('Service', $array);
         $queryMock = $this->getMockBuilder(Builder::class)
             ->disableOriginalConstructor()
             ->setMethods(['get'])
             ->getMock();
         $queryMock
             ->method('get')
             ->will($this->returnValue(new Collection(new ServiceEntity($array, $this->optimus))));

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

         $dbService = new DBService($factory, $this->optimus, $connectionMock);

         $this->assertSame($array, $dbService->getAll()->toArray());
    }

    public function testGetAllEmpty() {
         $array = [
            'name'       => 'New Service',
            'slug'       => 'new-service',
            'enabled'    => true,
            'created_at' => time(),
            // 'updated_at' => time()
         ];
         $factory = new Entity($this->optimus);
         $factory->create('Service', $array);
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

         $dbService = new DBService($factory, $this->optimus, $connectionMock);

         $this->assertEmpty($dbService->getAll()->toArray());
    }
}
