<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Setting as SettingEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBSetting;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Test\Unit\AbstractUnit;

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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->setExpectedException(NotFound::class);
        $dbSetting->findOne('', '', '');
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbSetting->getAllByCompanyId(1));
        $this->assertEmpty($dbSetting->getAllByCompanyId(1));
    }

    public function testGetAllBycompanyId() {
        $array = [
            [
                'section'       => 'NiceSetting',
                'property'      => 'niceProperty',
                'value'         => 'niceValue',
                'created_at'    => time(),
                'updated_at'    => time()
            ],
            [
                'section'       => 'ReallyNiceSetting',
                'property'      => 'realyNiceProperty',
                'value'         => 'realyNiceValue',
                'created_at'    => time(),
                'updated_at'    => time()
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
            ->will(
                $this->returnValue(
                    new Collection([
                        new SettingEntity($array[0]),
                        new SettingEntity($array[1])
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
        $dbSetting = new DBSetting($factory, $connectionMock);

        $this->assertEmpty($dbSetting->getAllByCompanyIdAndSection('', '')->toArray());
    }

    public function testGetAllByCompanyIdAndSection() {
        $array = [
            [
                'section'    => 'section1',
                'property'   => 'property1',
                'value'      => 'value1',
                'created_at' => time(),
                'updated_at' => time()
            ],
            [
                'section'    => 'section2',
                'property'   => 'property2',
                'value'      => 'value2',
                'created_at' => time(),
                'updated_at' => time()
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
            ->will($this->returnValue(new Collection([
                new SettingEntity($array[0]),
                new SettingEntity($array[1])
            ])));

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
        $this->assertEquals($array, $dbSetting->getAllByCompanyIdAndSection(1, 'section')->toArray());
    }
}
