<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Credential as CredentialEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBCredential;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Test\Unit\AbstractUnit;

class DBCredentialTest extends AbstractUnit {
    public function testFindByPubKeyNotFound() {
        $factory = new Entity();
        $factory->create('Credential', []);
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
            ->will($this->returnValue([1]));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbCredential = new DBCredential($factory, $connectionMock);

        $this->setExpectedException(NotFound::class);
        $dbCredential->findByPubKey(1);
    }

    public function testFindByPubKey() {
        $array = [
            'name'       => 'NiceCredential',
            'public'     => 'public',
            'slug'       => 'nice-credential',
            'created_at' => time(),
            'updated_at' => time()
        ];

        $factory = new Entity();
        $factory->create('Credential', []);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([new CredentialEntity($array)])));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue([1]));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));

        $dbCredential = new DBCredential($factory, $connectionMock);

        $this->assertEquals($array, $dbCredential->findByPubKey(1)->toArray());
    }

    public function testGetAllByCompanyIdEmpty() {
        $factory = new Entity();
        $factory->create('Credential', []);
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
            ->will($this->returnValue([1]));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbCredential = new DBCredential($factory, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbCredential->getAllByCompanyId(1));
        $this->assertEmpty($dbCredential->getAllByCompanyId(1));
    }

    public function testGetAllBycompanyId() {
        $array = [
            [
                'name'       => 'NiceCredential',
                'slug'       => 'nice-credential',
                'public'     => 'public',
                'created_at' => time(),
                'updated_at' => time()
            ],
            [
                'name'       => 'ReallyNiceCredential',
                'slug'       => 'really-nice-credential',
                'public'     => 'public2',
                'created_at' => time(),
                'updated_at' => time()
            ]
        ];

        $factory = new Entity();
        $factory->create('Credential', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will(
                $this->returnValue(
                    new Collection([
                        new CredentialEntity($array[0]),
                        new CredentialEntity($array[1])
                    ])
                )
            );
        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue([1]));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbCredential = new DBCredential($factory, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbCredential->getAllByCompanyId(1));
        $this->assertEquals($array, $dbCredential->getAllByCompanyId(1)->toArray());
    }

    public function testDeleteByCompanyId() {
        $factory = new Entity();
        $factory->create('Credential', []);
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
            ->will($this->returnValue([1]));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbCredential = new DBCredential($factory, $connectionMock);

        $this->assertEquals(3, $dbCredential->deleteByCompanyId(1));
    }

    public function testDeleteByPubKey() {
        $factory = new Entity();
        $factory->create('Credential', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'delete'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('delete')
            ->will($this->returnValue(2));
        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue([1]));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbCredential = new DBCredential($factory, $connectionMock);

        $this->assertEquals(2, $dbCredential->deleteByPubKey(1));
    }

}
