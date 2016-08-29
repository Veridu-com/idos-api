<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Controller;

use App\Command\Member\CreateNew;
use App\Command\Member\DeleteAll;
use App\Command\Member\DeleteOne;
use App\Command\Member\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\Members;
use App\Entity\Company as CompanyEntity;
use App\Entity\Member as MemberEntity;
use App\Entity\User as UserEntity;
use App\Factory\Command;
use App\Repository\DBMember;
use App\Repository\DBUser;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class MembersTest extends AbstractUnit {
    private function getCompanyEntity($id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new CompanyEntity(
            [
                'name'       => 'New Company',
                'id'         => $id,
                'slug'       => 'new-company',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }
    private function getUserEntity() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new UserEntity(
            [
                'id'         => 1,
                'username'   => 'Username',
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

        return new MemberEntity(
            [
                'user'       => [],
                'user_id'    => 1,
                'role'       => 'admin',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    public function testListAllWithFilterRole() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getQueryParam'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getCompanyEntity(1)));
        $requestMock
            ->method('getQueryParam')
            ->will($this->returnValue('admin, member'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyIdAndRole'])
            ->getMock();
        $dbMemberMock
            ->expects($this->once())
            ->method('getAllByCompanyIdAndRole')
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

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $memberMock->listAll($requestMock, $responseMock));
    }

    public function testListAllWithoutFilterRole() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getQueryParam'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getCompanyEntity(1)));
        $requestMock
            ->method('getQueryParam')
            ->will($this->returnValue(null));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $dbMemberMock
            ->expects($this->once())
            ->method('getAllByCompanyId')
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

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $memberMock->listAll($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['userName' => 'aUserName']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
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

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $memberMock->createNew($requestMock, $responseMock));
    }

    public function testUpdateOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue(1));
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['role' => 'admin']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
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
            ->will($this->onConsecutiveCalls(new UpdateOne(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $memberMock->UpdateOne($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue(1));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOne'])
            ->getMock();
        $dbMemberMock
            ->expects($this->once())
            ->method('findOne')
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

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $memberMock->getOne($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getCompanyEntity(0)));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
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

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $memberMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue(1));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbMemberMock = $this->getMockBuilder(DBMember::class)
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

        $memberMock = $this->getMockBuilder(Members::class)
            ->setConstructorArgs([$dbMemberMock, $dbUserMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

         $this->assertSame($responseMock, $memberMock->deleteOne($requestMock, $responseMock));
    }
}
