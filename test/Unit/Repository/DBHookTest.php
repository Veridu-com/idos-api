<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Company\Hook as HookEntity;
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
                $this->returnValue(
                    [
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

        $dbHook = new DBHook(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $result = $dbHook->getAllByCredentialId(1)->first();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($this->getAttributes(), $result->toArray());
    }

    public function testFindOneNotFound() {
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

        $dbHook = new DBHook(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->expectedException(NotFound::class);
        $dbHook->find(1);
    }

    public function testFindOne() {
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

        $dbHook = new DBHook(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $result = $dbHook->find(1);

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($this->getAttributes(), $result->toArray());
    }

    public function testDeleteOne() {
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

        $dbHook = new DBHook(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->assertSame(1, $dbHook->delete(1));
    }

    public function testDeleteByCredentialId() {
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

        $dbHook = new DBHook(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->assertSame(3, $dbHook->deleteByCredentialId(1));
    }
}
