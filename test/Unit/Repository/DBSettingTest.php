<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Company\Setting as SettingEntity;
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
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('first')
            ->will($this->returnValue(null));
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

        $dbSetting = new DBSetting(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->setExpectedException(NotFound::class);
        $dbSetting->find(0);
    }

    public function testGetAllByCompanyIdEmpty() {
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

        $dbSetting = new DBSetting(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $result = $dbSetting->getAllByCompanyId(1);

        $this->assertArrayHasKey('collection', $result);
        $this->assertInstanceOf(Collection::class, $result['collection']);

        // @FIXME - Problems testing the paginator with the Builder mock.
        // $this->assertInstanceOf(SettingEntity::class, $result['collection']->first());
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
                    new Collection(
                        [
                            new SettingEntity($array[0], $this->optimus),
                            new SettingEntity($array[1], $this->optimus)
                        ]
                    )
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

        $dbSetting = new DBSetting(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $result = $dbSetting->getAllByCompanyId(1);
        $this->assertInstanceOf(Collection::class, $result['collection']);

        // @FIXME - Problems testing the paginator with the Builder mock.
        // $this->assertInstanceOf(SettingEntity::class, $result['collection']->first());
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        // $this->assertEquals($array[0], $result['collection']->first()->toArray());
    }

    public function testGetAllByCompanyIdAndSectionEmpty() {
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

        $dbSetting = new DBSetting(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

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
                    new Collection(
                        [
                        new SettingEntity($array[0], $this->optimus),
                        new SettingEntity($array[1], $this->optimus)
                        ]
                    )
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

        $dbSetting = new DBSetting(
            new Entity($this->optimus),
            $this->optimus, $connectionMock
        );

        $this->assertInstanceOf(Collection::class, $dbSetting->getAllByCompanyIdAndSection(1, 'section'));
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        // $this->assertEquals($array, $dbSetting->getAllByCompanyIdAndSection(1, 'section')->toArray());
    }
}
