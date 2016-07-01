<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use Test\Unit\AbstractUnit;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBCompany;
use Illuminate\Database\Connection;

class DBCompanyTest extends AbstractUnit {
    public function testFindBySlugNotFound() {
        $factory = new Entity();
        $factory->create('Company', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('first')
            ->will($this->returnValue([]));
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbCompany->findBySlug('');
    }

    public function testFindBySlug() {
        $array = [
            'slug'       => 'slug',
            'id'         => 0,
            'name'       => 'company',
            'public_key' => 'public_key'
        ];

        $factory = new Entity();
        $factory->create('Company', []);
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->assertSame($array, $dbCompany->findBySlug('slug'));
    }

    public function testFindByPubKeyNotFound() {
        $factory = new Entity();
        $factory->create('Company', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('first')
            ->will($this->returnValue([]));
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbCompany->findByPubKey('');
    }

    public function testFindbyPubKey() {
        $array = [
            'public_key' => 'public_key',
            'slug'       => 'slug',
            'id'         => 0,
            'name'       => 'company'
         ];

        $factory = new Entity();
        $factory->create('Company', []);
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
         $dbCompany = new DBCompany($factory, $connectionMock);
         $this->assertSame($array, $dbCompany->findByPubKey('public_key'));
    }

    public function testFindByPrivKeyNotFound() {
        $factory = new Entity();
        $factory->create('Company', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('first')
            ->will($this->returnValue([]));
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbCompany->findByPrivKey('');
    }

    public function testFindByPrivKey() {
        $array = [
            'public_key' => 'public_key',
            'slug'       => 'slug',
            'id'         => 0,
            'name'       => 'company'
        ];

        $factory = new Entity();
        $factory->create('Company', []);
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
         $dbCompany = new DBCompany($factory, $connectionMock);
         $this->assertSame($array, $dbCompany->findByPrivKey('private_key'));
    }
}
