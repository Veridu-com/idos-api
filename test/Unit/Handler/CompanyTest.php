<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Handler;

use App\Command\Company\CreateNew;
use App\Command\Company\DeleteAll;
use App\Command\Company\DeleteOne;
use App\Command\Company\UpdateOne;
use App\Entity\Company as CompanyEntity;
use App\Event\Company\Created;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Company;
use App\Repository\CompanyInterface;
use App\Repository\DBCompany;
use App\Validator\Company as CompanyValidator;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class CompanyTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $validatorMock = $this
            ->getMockBuilder(CompanyValidator::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Company(
                $repositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryFactoryMock
            ->method('create')
            ->willReturn($repositoryMock);

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
            ->getMockBuilder(CompanyValidator::class)
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

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $emitterFactoryMock = $this
            ->getMockBuilder(Emitter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emitterFactoryMock
            ->method('create')
            ->willReturn($emitterMock);

        $container['emitterFactory'] = function () use ($emitterFactoryMock) {
            return $emitterFactoryMock;
        };

        Company::register($container);
        $this->assertInstanceOf(Company::class, $container[Company::class]);
    }

    public function testHandleCreateNewInvalidCompanyName() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $repositoryMock,
            new CompanyValidator(),
            $emitterMock
        );
        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->name = '';

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $companyEntity = new CompanyEntity(
            [
                'name'       => 'valid co',
                'slug'       => 'valid-co',
                'public_key' => 'test',
            ],
            $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->setMethods(['emit'])
            ->getMock();

        $emitterMock
            ->method('emit')
            ->will($this->returnValue(new Created($companyEntity)));

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company');

        $companyRepository = $this->getMockBuilder(DBCompany::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $companyRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($companyEntity);

        $handler = new Company(
            $companyRepository,
            new CompanyValidator(),
            $emitterMock
        );

        $command           = new CreateNew();
        $command->name     = 'valid co';
        $command->parentId = 1;

        $result = $handler->handleCreateNew($command);

        $this->assertSame('valid co', $result->name);
        $this->assertSame('valid-co', $result->slug);
        $this->assertNotEmpty($result->public_key);
        $this->assertSame(1, $result->parentId);
    }

    public function testHandleUpdateOne() {
        // forged created_at
        $createdAt     = time();
        $companyEntity = new CompanyEntity(
            [
                'id'         => 0,
                'name'       => 'New Company',
                'slug'       => 'new-company',
                'public_key' => 'public_key',
                'created_at' => $createdAt,
                'updated_at' => null
            ],
            $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company');

        $companyRepository = $this->getMockBuilder(DBCompany::class)
            ->setMethods(['find', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $companyRepository
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($companyEntity));
        $companyRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($companyEntity);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $companyRepository,
            new CompanyValidator(),
            $emitterMock
        );

        $command            = new UpdateOne();
        $command->name      = 'valid co';
        $command->companyId = 0;

        $result = $handler->handleUpdateOne($command);
        $this->assertInstanceOf(CompanyEntity::class, $result);
        $this->assertSame(0, $result->id);
        $this->assertSame('valid co', $result->name);
        $this->assertSame('valid-co', $result->slug);
        $this->assertSame('public_key', $result->publicKey);
        $this->assertSame($createdAt, $result->createdAt);
        $this->assertNotEmpty($result->updatedAt);
    }

    public function testHandleDeleteOneInvalidCompany() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $repositoryMock,
            new CompanyValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->company = null;

        $handler->handleDeleteOne($commandMock);
    }

    public function testHandleDeleteAllInvalidCompanySlug() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $repositoryMock,
            new CompanyValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->companySlug = '';

        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company');

        $companyRepository = $this->getMockBuilder(DBCompany::class)
            ->setMethods(['delete'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $companyRepository
            ->method('delete')
            ->will($this->returnValue(1));

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $companyRepository,
            new CompanyValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entity               = new CompanyEntity(['id' => 0], $this->optimus);
        $commandMock->company = $entity;

        $this->assertSame(1, $handler->handleDeleteOne($commandMock));
    }

    public function testHandleDeleteAllCompanyIdNotFound() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $repositoryMock,
            new CompanyValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = null;

        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company');

        $companyRepository = $this->getMockBuilder(DBCompany::class)
            ->setMethods(['deleteByParentId', 'getAllByParentId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $companyRepository
            ->method('deleteByParentId')
            ->will($this->returnValue(0));

        $collectionMock = $this
            ->getMockBuilder(Collection::class)
            ->getMock();

        $companyRepository
            ->method('getAllByParentId')
            ->will($this->returnValue($collectionMock));

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Company(
            $companyRepository,
            new CompanyValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->getMock();

        $commandMock->parentId = 0;

        $this->assertSame(0, $handler->handleDeleteAll($commandMock));
    }
}
