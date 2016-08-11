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
use App\Entity\Credential as CredentialEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Member;
use App\Repository\CredentialInterface;
use App\Repository\DBCredential;
use App\Repository\DBMember;
use App\Repository\DBUser;
use App\Repository\MemberInterface;
use App\Repository\UserInterface;
use App\Validator\Member as MemberValidator;
use Jenssegers\Optimus\Optimus;
use Slim\Container;
use Test\Unit\AbstractUnit;

class MemberTest extends AbstractUnit {
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
        return new MemberEntity(
            [
                'user'       => [],
                'user_id'    => 1,
                'username'   => 'userName',
                'role'       => 'admin',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }
    private function getUserEntity() {
        return new UserEntity(
            [
                'id'         => 1,
                'username'   => 'userName',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    private function getCredentialEntity() {
        return new CredentialEntity(
            [
                'id' => 1,
                'name' => 'New Credential',
                'slug' => 'new-credential',
                'public' => 'pubKey',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(MemberInterface::class)
            ->getMock();
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
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
                $credentialRepositoryMock,
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
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryFactoryMock
            ->expects($this->exactly(3))
            ->method('create')
            ->will($this->onConsecutiveCalls($repositoryMock, $credentialRepositoryMock, $userRepositoryMock));

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
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $handler = new Member(
            $repositoryMock,
            $credentialRepositoryMock,
            $userRepositoryMock,
            new MemberValidator()
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->credential = '';
        $commandMock->userName   = '';

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $memberEntity     = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Member');

        $repository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['create', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($memberEntity));
        $repository
            ->expects($this->once())
            ->method('save')
            ->willReturn($memberEntity);
        $credentialRepositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['findByPubKey'])
            ->getMock();
        $credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->returnValue($this->getCredentialEntity()));
        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['findOneBy'])
            ->getMock();
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->getUserEntity()));

        $handler = new Member(
            $repository,
            $credentialRepositoryMock,
            $userRepositoryMock,
            new MemberValidator()
        );

        $command               = new CreateNew();
        $command->userName     = 'userName';
        $command->role         = 'admin';
        $command->credential    = 'pubKey';

        $result = $handler->handleCreateNew($command);
        $this->assertSame($memberEntity, $result);
        $this->assertSame('admin', $result->role);
    }

    public function testHandleUpdateOne() {
        $memberEntity     = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Member');

        $repository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['create', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($this->getEntity()));
        $repository
            ->expects($this->once())
            ->method('save')
            ->willReturn($memberEntity);
        $credentialRepositoryMock = $this->getMockBuilder(CredentialInterface::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $handler = new Member(
            $repository,
            $credentialRepositoryMock,
            $userRepositoryMock,
            new MemberValidator()
        );

        $command               = new UpdateOne();
        $command->role         = 'admin';
        $command->memberId       = 1;

        $result = $handler->handleUpdateOne($command);
        $this->assertSame('admin', $result->role);
        $this->assertInstanceOf(MemberEntity::class, $result);
        $this->assertSame(['role' => 'admin'], $result);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Member');

        $repository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['delete'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->method('deleteOne')
            ->will($this->returnValue(1));
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = new Member(
            $repository,
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

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Member');

        $repository = $this->getMockBuilder(DBMember::class)
            ->setMethods(['deleteByCompanyId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->method('deleteByCompanyId')
            ->will($this->returnValue(1));
        $credentialRepositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['findByPubKey'])
            ->getMock();
        $credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->returnValue(
                new CredentialEntity(
                    [
                        'id' => 1,
                        'companyId' => 1
                    ],
                    $this->optimus
                )
            ));
        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->setMethods(null)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $handler = new Member(
            $repository,
            $credentialRepositoryMock,
            $userRepositoryMock,
            new MemberValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->credential = 'pubKey';

        $this->assertEquals(1, $handler->handleDeleteAll($commandMock));
    }
}
