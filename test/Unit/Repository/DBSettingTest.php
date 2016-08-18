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
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBSettingTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindOneNotFound() {
        $factory = new Entity($this->optimus);
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
        $dbSetting = new DBSetting($factory, $this->optimus, $connectionMock);

        $this->setExpectedException(NotFound::class);
        $dbSetting->findOne(0, '', '');
    }

    public function testGetAllByCompanyIdEmpty() {
        $factory = new Entity($this->optimus);
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
            ->will($this->returnValue(new Collection([new SettingEntity([], $this->optimus)])));
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
        $dbSetting = new DBSetting($factory, $this->optimus, $connectionMock);

        $result = $dbSetting->getAllByCompanyId(1);
        $this->assertArrayHasKey('collection', $result);
        $this->assertInstanceOf(Collection::class, $result['collection']);
        $this->assertInstanceOf(SettingEntity::class, $result['collection']->first());
    }

    public function testGetAllByCompanyId() {
        $array = [
            [
                'section'    => 'NiceSetting',
                'property'   => 'niceProperty',
                'value'      => 'niceValue',
                'created_at' => time(),
                'updated_at' => time()
            ],
            [
                'section'    => 'ReallyNiceSetting',
                'property'   => 'realyNiceProperty',
                'value'      => 'realyNiceValue',
                'created_at' => time(),
                'updated_at' => time()
            ]
        ];

        $factory = new Entity($this->optimus);
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
                        new SettingEntity($array[0], $this->optimus),
                        new SettingEntity($array[1], $this->optimus)
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
        $dbSetting = new DBSetting($factory, $this->optimus, $connectionMock);

        $result = $dbSetting->getAllByCompanyId(1);
        $this->assertInstanceOf(Collection::class, $result['collection']);
        $this->assertInstanceOf(SettingEntity::class, $result['collection']->first());
        $this->assertSame($array[0], $result['collection']->first()->toArray());
    }

    public function testGetAllByCompanyIdAndSectionEmpty() {
        $factory = new Entity($this->optimus);
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
        $dbSetting = new DBSetting($factory, $this->optimus, $connectionMock);

        $this->assertEmpty($dbSetting->getAllByCompanyIdAndSection(0, '')->toArray());
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

        $factory = new Entity($this->optimus);
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
                new SettingEntity($array[0], $this->optimus),
                new SettingEntity($array[1], $this->optimus)
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

        $dbSetting = new DBSetting($factory, $this->optimus, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbSetting->getAllByCompanyIdAndSection(1, 'section'));
        $this->assertEquals($array, $dbSetting->getAllByCompanyIdAndSection(1, 'section')->toArray());
    }
}
