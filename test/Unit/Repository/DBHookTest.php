<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Hook as HookEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBHook;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBHookTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getEntity() {
        return new HookEntity(
            [
                'id'            => 1,
                'credential_id' => 1,
                'trigger'       => 'trigger.test',
                'url'           => 'http://example.com/test.php',
                'subscribed'    => false,
                'created_at'    => time(),
                'updated_at'    => time()
            ],
            $this->optimus
        );
    }

    private function getAttributes() {
        return [
            'id'         => null,
            'trigger'    => 'trigger.test',
            'url'        => 'http://example.com/test.php',
            'subscribed' => false,
            'created_at' => time(),
            'updated_at' => time()
        ];
    }

    public function testGetAllByCredentialId() {
        $factory = new Entity($this->optimus);
        $factory->create('Hook', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['join', 'where', 'get', 'whereIn'])
            ->getMock();
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('whereIn')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will(
                $this->returnValue([
                    new HookEntity(
                        [
                            'id'            => 1,
                            'credential_id' => 1,
                            'trigger'       => 'trigger.test',
                            'url'           => 'http://example.com/test.php',
                            'subscribed'    => false,
                            'created_at'    => time(),
                            'updated_at'    => time()
                        ],
                        $this->optimus
                    )
                ])
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
        $dbHook = new DBHook($factory, $this->optimus, $connectionMock);
        $result = $dbHook->getAllByCredentialId(1)->first();
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testFindOneNotFound() {
        $factory = new Entity($this->optimus);
        $factory->create('Hook', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([])));
        $queryMock
            ->method('first')
            ->will($this->returnValue(null));

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
        $dbHook = new DBHook($factory, $this->optimus, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbHook->find(1);
    }

    public function testFindOne() {
        $factory = new Entity($this->optimus);
        $factory->create('Hook', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([$this->getEntity()])));
        $queryMock
            ->method('first')
            ->will($this->returnValue($this->getEntity()));

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
        $dbHook = new DBHook($factory, $this->optimus, $connectionMock);

        $result = $dbHook->find(1);
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testDeleteOne() {
        $factory = new Entity($this->optimus);
        $factory->create('Hook', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'delete'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('delete')
            ->will($this->returnValue(1));

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
        $dbHook = new DBHook($factory, $this->optimus, $connectionMock);
        $this->assertEquals(1, $dbHook->delete(1));
    }

    public function testDeleteByCredentialId() {
        $factory = new Entity($this->optimus);
        $factory->create('Hook', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'delete'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('delete')
            ->will($this->returnValue(3));

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
        $dbHook = new DBHook($factory, $this->optimus, $connectionMock);
        $this->assertEquals(3, $dbHook->deleteByCredentialId(1));
    }
}
