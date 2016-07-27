<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Company as CompanyEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBCompany;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Test\Unit\AbstractUnit;

class DBCompanyTest extends AbstractUnit {
    public function testFindBySlugNotFound() {
        $factory = new Entity();
        $factory->create('Company', []);
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbCompany->findBySlug('');
    }

    public function testFindBySlug() {
        $array = [
            'name'       => 'company',
            'slug'       => 'slug',
            'public_key' => 'public_key',
            'created_at' => time(),
            'updated_at' => time()
        ];

        $factory = new Entity();
        $factory->create('Company', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(
                new Collection([
                    new CompanyEntity($array)
                ])
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

        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->assertSame($array, $dbCompany->findBySlug('slug')->toArray());
    }

    public function testFindByPubKeyNotFound() {
        $factory = new Entity();
        $factory->create('Company', []);
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbCompany->findByPubKey('');
    }

    public function testFindbyPubKey() {
        $array = [
            'name'       => 'company',
            'slug'       => 'slug',
            'public_key' => 'public_key',
            'created_at' => time(),
            'updated_at' => time()
         ];

        $factory = new Entity();
        $factory->create('Company', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([new CompanyEntity($array)])));
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
         $this->assertSame($array, $dbCompany->findByPubKey('public_key')->toArray());
    }

    public function testFindByPrivKeyNotFound() {
        $factory = new Entity();
        $factory->create('Company', []);
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
        $dbCompany = new DBCompany($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbCompany->findByPrivKey('');
    }

    public function testFindByPrivKey() {
        $array = [
            'name'       => 'company',
            'slug'       => 'slug',
            'public_key' => 'public_key',
            'created_at' => time(),
            'updated_at' => time()
        ];

        $factory = new Entity();
        $factory->create('Company', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([new CompanyEntity($array)])));
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
         $this->assertSame($array, $dbCompany->findByPrivKey('private_key')->toArray());
    }
}
