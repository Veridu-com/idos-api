<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Controller;

use App\Command\ResponseDispatch;
use App\Command\Tag\CreateNew;
use App\Command\Tag\DeleteAll;
use App\Command\Tag\DeleteOne;
use App\Controller\Tags;
use App\Entity\Tag as TagEntity;
use App\Entity\User as UserEntity;
use App\Factory\Command;
use App\Repository\DBTag;
use App\Repository\DBUser;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class TagsTest extends AbstractUnit {
    private function getUserEntity() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new UserEntity(
            [
                'id'         => 1,
                'username'   => 'target-user',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }
    private function getEntity() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new TagEntity(
            [
                'user_id'    => 1,
                'name'       => 'Tag Test',
                'slug'       => 'tag-test',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    public function testListAllWithFilterTag() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getQueryParam'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getUserEntity()));
        $requestMock
            ->method('getQueryParam')
            ->will($this->returnValue('tag-test,tag-test2'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbTagMock = $this->getMockBuilder(DBTag::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByUserIdAndTagSlugs'])
            ->getMock();
        $dbTagMock
            ->expects($this->once())
            ->method('getAllByUserIdAndTagSlugs')
            ->will($this->returnValue(new Collection([$this->getEntity()])));
        $dbUserMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagMock = $this->getMockBuilder(Tags::class)
            ->setConstructorArgs([$dbTagMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $tagMock->listAll($requestMock, $responseMock));
    }

    public function testListAllWithoutFilterTag() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getQueryParam'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getUserEntity()));
        $requestMock
            ->method('getQueryParam')
            ->will($this->returnValue('tag-test'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbTagMock = $this->getMockBuilder(DBTag::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByUserIdAndTagSlugs'])
            ->getMock();
        $dbTagMock
            ->expects($this->once())
            ->method('getAllByUserIdAndTagSlugs')
            ->will($this->returnValue(new Collection([$this->getEntity()])));
        $dbUserMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagMock = $this->getMockBuilder(Tags::class)
            ->setConstructorArgs([$dbTagMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $tagMock->listAll($requestMock, $responseMock));
    }

public function testCreateNew() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParsedBody', 'getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['name' => 'test-tag']));
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getUserEntity()));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbTagMock = $this->getMockBuilder(DBTag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dbUserMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new CreateNew(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagMock = $this->getMockBuilder(Tags::class)
            ->setConstructorArgs([$dbTagMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $tagMock->createNew($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will($this->onConsecutiveCalls($this->getUserEntity(), 'test-tag'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbTagMock = $this->getMockBuilder(DBTag::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneByUserIdAndSlug'])
            ->getMock();
        $dbTagMock
            ->expects($this->once())
            ->method('findOneByUserIdAndSlug')
            ->will($this->returnValue($this->getEntity()));
        $dbUserMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagMock = $this->getMockBuilder(Tags::class)
            ->setConstructorArgs([$dbTagMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $tagMock->getOne($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->getUserEntity()));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbTagMock = $this->getMockBuilder(DBTag::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $dbUserMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls(2, $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteAll(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagMock = $this->getMockBuilder(Tags::class)
            ->setConstructorArgs([$dbTagMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $tagMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will($this->onConsecutiveCalls($this->getUserEntity(), 'test-tag'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbTagMock = $this->getMockBuilder(DBTag::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $dbUserMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteOne(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tagMock = $this->getMockBuilder(Tags::class)
            ->setConstructorArgs([$dbTagMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $tagMock->deleteOne($requestMock, $responseMock));
    }
}
