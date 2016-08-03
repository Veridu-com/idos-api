<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Handler;

use App\Command\Member\CreateNew;
use App\Command\Member\DeleteAll;
use App\Command\Member\DeleteOne;
use App\Command\Member\UpdateOne;
use App\Entity\Member as MemberEntity;
use App\Entity\User as UserEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Member;
use App\Repository\DBMember;
use App\Repository\DBUser;
use App\Repository\MemberInterface;
use App\Repository\UserInterface;
use App\Validator\Member as MemberValidator;
use Slim\Container;
use Test\Unit\AbstractUnit;

class MemberTest extends AbstractUnit {
    private function getEntity() {
        return new MemberEntity(
            [
                'user'       => [],
                'user_id'    => 1,
                'role'       => 'admin',
                'created_at' => time(),
                'updated_at' => time()
            ]
        );
    }
    private function getUserEntity() {
        return new UserEntity(
            [
                'id'         => 1,
                'username'   => 'userName',
                'created_at' => time(),
                'updated_at' => time()
            ]
        );
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(MemberInterface::class)
            ->getMock();
        $userRepositoryMock = $this
            ->getMockBuilder(UserInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(MemberValidator::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Member(
                $repositoryMock,
                $userRepositoryMock,
                $validatorMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(MemberInterface::class)
            ->getMock();
        $userRepositoryMock = $this
            ->getMockBuilder(UserInterface::class)
            ->getMock();

        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls($repositoryMock, $userRepositoryMock));

        $container['repositoryFactory'] = function () use ($repositoryFactoryMock) {
            return $repositoryFactoryMock;
        };

        $validatorMock = $this
            ->getMockBuilder(MemberValidator::class)
            ->getMock();

        $validatorFactoryMock = $this
            ->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $validatorFactoryMock
            ->method('create')
            ->willReturn($validatorMock);

        $container['validatorFactory'] = function () use ($validatorFactoryMock) {
            return $validatorFactoryMock;
        };

        Member::register($container);
        $this->assertInstanceOf(Member::class, $container[Member::class]);
    }

    public function testHandleCreateNewInvalidParam() {
        $repositoryMock = $this
            ->getMockBuilder(MemberInterface::class)
            ->getMock();
        $userRepositoryMock = $this
            ->getMockBuilder(UserInterface::class)
            ->getMock();

        $handler = new Member(
            $repositoryMock,
            $userRepositoryMock,
            new MemberValidator()
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->companyId = 0;
        $commandMock->userName  = '';

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $memberEntity     = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('Member');

        $memberRepository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['create', 'save'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $memberRepository
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($memberEntity));
        $memberRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($memberEntity);
        $userRepository = $this->getMockBuilder(DBUser::class)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->setMethods(['findOneBy'])
            ->getMock();
        $userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->getUserEntity()));

        $handler = new Member(
            $memberRepository,
            $userRepository,
            new MemberValidator()
        );

        $command               = new CreateNew();
        $command->userName     = 'userName';
        $command->role         = 'admin';
        $command->companyId    = 1;

        $result = $handler->handleCreateNew($command);
        $this->assertSame($this->getUserEntity()->toArray(), $result->user);
        $this->assertSame('admin', $result->role);
    }

    public function testHandleUpdateOne() {
        $memberEntity     = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('Member');

        $memberRepository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['create', 'save'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $memberRepository
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($this->getEntity()));
        $memberRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($memberEntity);
        $userRepository = $this->getMockBuilder(DBUser::class)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();

        $handler = new Member(
            $memberRepository,
            $userRepository,
            new MemberValidator()
        );

        $command               = new UpdateOne();
        $command->userName     = 'userName';
        $command->role         = 'admin';
        $command->companyId    = 1;
        $command->userId       = 1;

        $result = $handler->handleUpdateOne($command);
        $this->assertSame($this->getUserEntity()->toArray(), $result->user);
        $this->assertSame('admin', $result->role);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('Member');

        $memberRepository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['delete'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $memberRepository
            ->method('deleteOne')
            ->will($this->returnValue(1));

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = new Member(
            $memberRepository,
            $userRepositoryMock,
            new MemberValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 1;
        $commandMock->userId    = 1;

        $this->assertEquals(1, $handler->handleDeleteOne($commandMock));
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('Member');

        $memberRepository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['deleteByCompanyId'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $memberRepository
            ->method('deleteByCompanyId')
            ->will($this->returnValue(1));

        $userRepository = $this->getMockBuilder(DBUser::class)
            ->setMethods(null)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();

        $handler = new Member(
            $memberRepository,
            $userRepository,
            new MemberValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 1;
        $commandMock->userId    = 1;

        $this->assertEquals(1, $handler->handleDeleteAll($commandMock));
    }
}
