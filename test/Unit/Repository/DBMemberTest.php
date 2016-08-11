<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Member as MemberEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBMember;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBMemberTest extends AbstractUnit {
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
        return new MemberEntity(
            [
                'id' => 1,
                'companyId' => 1,
                'user_id' => 1,
                'role' => 'admin',
                'created_at' => time(),
                'updated_at' => time(),
                'user.username' =>'userName',
                'user.created_at' => time()
            ],
            $this->optimus
        );
    }

    private function getAttributes() {
        return [
            'id' => null,
            'user' => [
                'username' =>'userName',
                'created_at' => time()
            ],
            'role' => 'admin',
            'created_at' => time(),
        ];
    }

    public function testGetAllBycompanyId() {
        $factory = new Entity($this->optimus);
        $factory->create('Member', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['join', 'where', 'get'])
            ->getMock();
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will(
                $this->returnValue(
                    new MemberEntity(
                        [
                            'id' => 1,
                            'companyId' => 1,
                            'user_id' => 1,
                            'role' => 'admin',
                            'created_at' => time(),
                            'updated_at' => time(),
                            'user.username' =>'userName',
                            'user.created_at' => time()
                        ],
                        $this->optimus
                    )
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
        $dbMember = new DBMember($factory, $this->optimus, $connectionMock);
        $result = $dbMember->getAllByCompanyId(1)->first();
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testGetAllBycompanyIdAndRole() {
        $factory = new Entity($this->optimus);
        $factory->create('Member', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([$this->getEntity()]));
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
        $dbMember = new DBMember($factory, $this->optimus, $connectionMock);
        $result = $dbMember->getAllByCompanyIdAndRole(1, ['admin'])->first();
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testFindOneNotFound() {
        $factory = new Entity($this->optimus);
        $factory->create('Member', []);
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
        $dbMember = new DBMember($factory, $this->optimus, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbMember->findOne(0, 1);
    }

    public function testfFindOne() {
        $factory = new Entity($this->optimus);
        $factory->create('Member', []);
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
        $dbMember = new DBMember($factory, $this->optimus, $connectionMock);

        $result = $dbMember->findOne(0, 1);
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testDeleteOne() {
        $factory = new Entity($this->optimus);
        $factory->create('Member', []);
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
        $dbMember = new DBMember($factory, $this->optimus, $connectionMock);
        $this->assertEquals(1, $dbMember->deleteOne(0, 1));
    }

    public function testDeleteByCompanyId() {
        $factory = new Entity($this->optimus);
        $factory->create('Member', []);
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
        $dbMember = new DBMember($factory, $this->optimus, $connectionMock);
        $this->assertEquals(3, $dbMember->deleteByCompanyId(1));
    }
}
