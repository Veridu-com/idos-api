<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBSetting;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Test\Unit\AbstractUnit;
use App\Entity\Setting as SettingEntity;

class DBSettingTest extends AbstractUnit {

    public function testFindOneNotFound() {
        $factory = new Entity();
        $factory->create('Setting', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([]));
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->setExpectedException(NotFound::class);
        $dbSetting->findOne('','', '');
    }

    public function testGetAllByCompanyIdEmpty() {
        $factory = new Entity();
        $factory->create('Setting', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([]));
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbSetting->getAllByCompanyId(1));
        $this->assertEmpty($dbSetting->getAllByCompanyId(1));
    }

    public function testGetAllBycompanyId() {
        $array = [
            [
                'companyId'  => 1,
                'name'       => 'NiceSetting',
                'created_at' => time()
            ],
            [
                'companyId'  => 1,
                'name'       => 'ReallyNiceSetting',
                'created_at' => time()
            ]
        ];

        $factory = new Entity();
        $factory->create('Setting', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue($array));
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbSetting->getAllByCompanyId(1));
        $this->assertEquals($array, $dbSetting->getAllByCompanyId(1)->toArray());
    }

    public function testGetAllByCompanyIdAndSectionEmpty() {
        $factory = new Entity();
        $factory->create('Setting', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([]));
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->assertEmpty($dbSetting->getAllByCompanyIdAndSection('', '')->toArray());
    }

    public function testGetAllByCompanyIdAndSection() {
        $factory = new Entity();
        $factory->create('Setting', []);
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
                    [
                        'companyId' => 1,
                        'section' => 'section',
                        'property' => 'property',
                        'value' => 'value'
                    ]
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbSetting->getAllByCompanyIdAndSection(1, 'section'));
        $array = $dbSetting->getAllByCompanyIdAndSection('', '')->toArray();
        $this->assertArrayHasKey('companyId', $array);
        $this->assertEquals(1, $array['companyId']);
        $this->assertArrayHasKey('section', $array);
        $this->assertEquals('section', $array['section']);
        $this->assertArrayHasKey('property', $array);
        $this->assertEquals('property', $array['property']);
        $this->assertArrayHasKey('value', $array);
        $this->assertEquals('value', $array['value']);
    }
}
