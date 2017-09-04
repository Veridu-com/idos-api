<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Handler;

use App\Command\Company\Credential\CreateNew;
use App\Command\Company\Credential\DeleteAll;
use App\Command\Company\Credential\DeleteOne;
use App\Command\Company\Credential\UpdateOne;
use App\Entity\Company\Credential as CredentialEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Company\Credential;
use App\Handler\HandlerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\DBCredential;
use App\Validator\Company\Credential as CredentialValidator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class CredentialTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->emitter = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(CredentialValidator::class)
            ->getMock();

        $this->assertInstanceOf(
            HandlerInterface::class,
            new Credential(
                $repositoryMock,
                $validatorMock,
                $this->emitter
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();

        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryFactoryMock
            ->method('create')
            ->willReturn($repositoryMock);

        $container['repositoryFactory'] = function () use ($repositoryFactoryMock) {
            return $repositoryFactoryMock;
        };

        $validatorMock = $this
            ->getMockBuilder(CredentialValidator::class)
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

        $emitter                   = $this->emitter;
        $container['eventEmitter'] = function () use ($emitter) {
            return $this->emitter;
        };

        Credential::register($container);
        $this->assertInstanceOf(Credential::class, $container[Credential::class]);
    }

    public function testHandleCreateNew() {
        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Credential');

        $builderMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['query'])
            ->getMock();
        $credentialRepository
            ->method('query')
            ->will($this->returnValue($builderMock));

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator(),
            $this->emitter
        );

        $command             = new CreateNew();
        $command->name       = 'valid cred';
        $command->companyId  = 1;
        $command->production = false;

        $result = $handler->handleCreateNew($command);
        $this->assertSame('valid cred', $result->name);
        $this->assertSame('valid-cred', $result->slug);
        $this->assertFalse($result->production);
        $this->assertNotEmpty($result->public);
    }

    public function testHandleUpdateOne() {
        // forged created_at
        $createdAt        = time();
        $credentialEntity = new CredentialEntity(
            [
                'id'         => 0,
                'name'       => 'valid cred',
                'slug'       => 'valid-cred',
                'public'     => 'public',
                'private'    => 'private',
                'created_at' => $createdAt,
                'updated_at' => null
            ],
            $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Credential');

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['query', 'find'])
            ->getMock();
        $credentialRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($credentialEntity);
        $credentialRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($credentialEntity);

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator(),
            $this->emitter
        );

        $command               = new UpdateOne();
        $command->name         = 'valid cred';
        $command->credentialId = 0;

        $result = $handler->handleUpdateOne($command);
        $this->assertSame(0, $result->id);
        $this->assertSame('valid cred', $result->name);
        $this->assertSame('valid-cred', $result->slug);
        $this->assertSame('public', $result->public);
        $this->assertSame('private', $result->private);
        $this->assertSame($createdAt, $result->createdAt);
        $this->assertNotEmpty($result->updatedAt);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Credential');

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['delete', 'find'])
            ->getMock();

        $credentialRepository
            ->method('delete')
            ->will($this->returnValue(1));

        $entityMock = $this->getMockBuilder(CredentialEntity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $credentialRepository
            ->method('find')
            ->will($this->returnValue($entityMock));

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator(),
            $this->emitter
        );

        $command               = new DeleteOne();
        $command->credentialId = 0;

        $this->assertSame(1, $handler->handleDeleteOne($command));
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Credential');

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['deleteByCompanyId', 'findByCompanyId'])
            ->getMock();

        $credentialRepository
            ->method('deleteByCompanyId')
            ->will($this->returnValue(3));

        $credentialRepository
            ->method('deleteByCompanyId')
            ->will(
                $this->returnValue(
                    new Collection(
                        [
                            [
                                'id'   => 1,
                                'name' => 'credential 1',
                            ],
                            [
                                'id'   => 2,
                                'name' => 'credential 2',
                            ],
                            [
                                'id'   => 3,
                                'name' => 'credential 3',
                            ],
                        ]
                    )
                )
            );

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator(),
            $this->emitter
        );

        $command            = new DeleteAll();
        $command->companyId = 0;

        $this->assertSame(3, $handler->handleDeleteAll($command));
    }
}
