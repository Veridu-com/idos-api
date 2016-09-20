<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Handler;

use App\Command\Company\Hook\CreateNew;
use App\Command\Company\Hook\DeleteAll;
use App\Command\Company\Hook\DeleteOne;
use App\Command\Company\Hook\UpdateOne;
use App\Entity\Company as CompanyEntity;
use App\Entity\Company\Credential as CredentialEntity;
use App\Entity\Company\Hook as HookEntity;
use App\Event\Company\Hook\Created;
use App\Event\Company\Hook\Deleted;
use App\Event\Company\Hook\DeletedMulti;
use App\Event\Company\Hook\Updated;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Company\Hook;
use App\Repository\Company\CredentialInterface;
use App\Repository\DBCredential;
use App\Repository\DBHook;
use App\Repository\Company\HookInterface;
use App\Validator\Company\Hook as HookValidator;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class HookTest extends AbstractUnit {
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
        return new HookEntity(
            [
                'id'            => 1,
                'credential_id' => 1,
                'trigger'       => 'trigger.test',
                'url'           => 'http://example.com/test.php',
                'subscribed'    => false,
                'created_at'    => time(),
                'updated_at'    => time()
            ],
            $this->optimus
        );
    }

    private function getCompanyEntity() {
        return new CompanyEntity(
            [
                'id'         => 1,
                'name'       => 'Company Test',
                'slug'       => 'company-test',
                'public_key' => '4c9184f37cff01bcdc32dc486ec36961',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    private function getCredentialEntity() {
        return new CredentialEntity(
            [
                'id'         => 1,
                'companyId'  => 1,
                'name'       => 'New Credential',
                'slug'       => 'new-credential',
                'public'     => '4c9184f37cff01bcdc32dc486ec36961',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(HookInterface::class)
            ->getMock();
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(HookValidator::class)
            ->getMock();
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Hook(
                $repositoryMock,
                $credentialRepositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(HookInterface::class)
            ->getMock();
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls($repositoryMock, $credentialRepositoryMock));
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $container['repositoryFactory'] = function () use ($repositoryFactoryMock) {
            return $repositoryFactoryMock;
        };

        $container['eventEmitter'] = function () use ($emitterMock) {
            return $emitterMock;
        };

        $validatorMock = $this
            ->getMockBuilder(HookValidator::class)
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

        Hook::register($container);
        $this->assertInstanceOf(Hook::class, $container[Hook::class]);
    }

    public function testHandleCreateNewInvalidParam() {
        $repositoryMock = $this
            ->getMockBuilder(HookInterface::class)
            ->getMock();
        $credentialRepositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();
        $handler = new Hook(
            $repositoryMock,
            $credentialRepositoryMock,
            new HookValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->trigger          = '';
        $commandMock->url              = '';
        $commandMock->subscribed       = '';
        $commandMock->credentialPubKey = '';
        $commandMock->company          = '';

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $hookEntity       = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Hook');

        $repository = $this->getMockBuilder(DBHook::class)
            ->setMethods(['create', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($hookEntity));
        $repository
            ->expects($this->once())
            ->method('save')
            ->willReturn($hookEntity);
        $credentialRepositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['findByPubKey'])
            ->getMock();
        $credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->returnValue($this->getCredentialEntity()));
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->setMethods(['emit'])
            ->getMock();
        $emitterMock
            ->method('emit')
            ->will($this->returnValue(new Created($hookEntity)));

        $handler = new Hook(
            $repository,
            $credentialRepositoryMock,
            new HookValidator(),
            $emitterMock
        );

        $commandMock                   = new CreateNew();
        $commandMock->trigger          = 'trigger.test';
        $commandMock->url              = 'http://example.com/test.php';
        $commandMock->subscribed       = false;
        $commandMock->credentialPubKey = '4c9184f37cff01bcdc32dc486ec36961';
        $commandMock->company          = $this->getCompanyEntity();

        $result = $handler->handleCreateNew($commandMock);
        $this->assertSame($hookEntity, $result);
    }

    public function testHandleUpdateOne() {
        $hookEntity       = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Hook');

        $repository = $this->getMockBuilder(DBHook::class)
            ->setMethods(['create', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->getEntity()));
        $repository
            ->expects($this->once())
            ->method('save')
            ->willReturn($hookEntity);
        $credentialRepositoryMock = $this->getMockBuilder(CredentialInterface::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->setMethods(['emit'])
            ->getMock();
        $emitterMock
            ->method('emit')
            ->will($this->returnValue(new Updated($hookEntity)));

        $handler = new Hook(
            $repository,
            $credentialRepositoryMock,
            new HookValidator(),
            $emitterMock
        );

        $command                   = new UpdateOne();
        $command->hookId           = 1;
        $command->trigger          = 'trigger.testChanged';
        $command->url              = 'http://example.com/changed.php';
        $command->subscribed       = true;
        $command->credentialPubKey = '4c9184f37cff01bcdc32dc486ec36961';
        $command->company          = $this->getCompanyEntity();

        $result = $handler->handleUpdateOne($command);
        $this->assertInstanceOf(HookEntity::class, $result);
        $this->assertSame(
            [
            'trigger'    => 'trigger.testChanged',
            'url'        => 'http://example.com/changed.php',
            'subscribed' => true,
            'created_at' => $hookEntity->created_at
            ], $result
        );
    }

    public function testHandleDeleteOne() {
        $hookEntity       = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Hook');

        $repository = $this->getMockBuilder(DBHook::class)
            ->setMethods(['find', 'delete'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->getEntity()));
        $repository
            ->method('delete')
            ->will($this->returnValue(1));
        $credentialRepositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['findByPubKey'])
            ->getMock();
        $credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->returnValue($this->getCredentialEntity()));
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->setMethods(['emit'])
            ->getMock();
        $emitterMock
            ->method('emit')
            ->will($this->returnValue(new Deleted($hookEntity)));

        $handler = new Hook(
            $repository,
            $credentialRepositoryMock,
            new HookValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->hookId           = 1;
        $commandMock->credentialPubKey = '4c9184f37cff01bcdc32dc486ec36961';
        $commandMock->company          = $this->getCompanyEntity();

        $this->assertSame(1, $handler->handleDeleteOne($commandMock));
    }

    public function testHandleDeleteAll() {
        $hookEntity       = $this->getEntity();
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Hook');

        $repository = $this->getMockBuilder(DBHook::class)
            ->setMethods(['getAllByCredentialId', 'deleteByCredentialId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $repository
            ->method('getAllByCredentialId')
            ->will($this->returnValue(new Collection([$hookEntity])));
        $repository
            ->method('deleteByCredentialId')
            ->will($this->returnValue(1));
        $credentialRepositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['findByPubKey'])
            ->getMock();
        $credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->returnValue($this->getCredentialEntity()));
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->setMethods(['emit'])
            ->getMock();
        $emitterMock
            ->method('emit')
            ->will($this->returnValue(new DeletedMulti(new Collection([$hookEntity]))));

        $handler = new Hook(
            $repository,
            $credentialRepositoryMock,
            new HookValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->credentialPubKey = '4c9184f37cff01bcdc32dc486ec36961';
        $commandMock->company          = $this->getCompanyEntity();

        $this->assertSame(1, $handler->handleDeleteAll($commandMock));
    }
}
