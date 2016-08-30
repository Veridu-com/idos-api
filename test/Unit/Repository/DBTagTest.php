<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Tag as TagEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBTag;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBTagTest extends AbstractUnit {
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
        return new TagEntity(
            [
                'id'         => 1,
                'user_id'    => 1,
                'name'       => 'Test Tag',
                'slug'       => 'test-tag',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    private function getAttributes() {
        return [
            'id'         => null,
            'name'       => 'Test Tag',
            'slug'       => 'test-tag',
            'created_at' => time(),
            'updated_at' => time()
        ];
    }

    public function testGetAllByUserId() {
        $factory = new Entity($this->optimus);
        $factory->create('Tag', []);
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
                    new TagEntity(
                        [
                            'id'         => 1,
                            'user_id'    => 1,
                            'name'       => 'Test Tag',
                            'slug'       => 'test-tag',
                            'created_at' => time(),
                            'updated_at' => time()
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
        $dbTag  = new DBTag($factory, $this->optimus, $connectionMock);
        $result = $dbTag->getAllByUserId(1)->first();
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testGetAllByUserIdAndTagNames() {
        $factory = new Entity($this->optimus);
        $factory->create('Tag', []);
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
        $dbTag  = new DBTag($factory, $this->optimus, $connectionMock);
        $result = $dbTag->getAllByUserIdAndTagSlugs(1, ['tag-test'])->first();
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testFindOneNotFound() {
        $factory = new Entity($this->optimus);
        $factory->create('Tag', []);
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
        $dbTag = new DBTag($factory, $this->optimus, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbTag->findOneByUserIdAndSlug(1, 'test-tag');
    }

    public function testfFindOne() {
        $factory = new Entity($this->optimus);
        $factory->create('Tag', []);
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
        $dbTag = new DBTag($factory, $this->optimus, $connectionMock);

        $result = $dbTag->findOneByUserIdAndSlug(1, 'test-tag');
        $this->assertSame($this->getAttributes(), $result->toArray());
    }

    public function testDeleteOne() {
        $factory = new Entity($this->optimus);
        $factory->create('Tag', []);
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
        $dbTag = new DBTag($factory, $this->optimus, $connectionMock);
        $this->assertEquals(1, $dbTag->deleteOneByUserIdAndSlug(1, 'test-tag'));
    }

    public function testDeleteByUserId() {
        $factory = new Entity($this->optimus);
        $factory->create('Tag', []);
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
        $dbTag = new DBTag($factory, $this->optimus, $connectionMock);
        $this->assertEquals(3, $dbTag->deleteByUserId(1));
    }
}
