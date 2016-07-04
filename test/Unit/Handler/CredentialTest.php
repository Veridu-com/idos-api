<?php

namespace Test\Unit\Handler;

use Test\Unit\AbstractUnit;
use App\Handler\Credential;
use App\Repository\CredentialInterface;
use App\Validator\Credential as CredentialValidator;
use App\Factory\Entity as EntityFactory;
use App\Entity\Credential as CredentialEntity;
use Slim\Container;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Command\Credential\CreateNew;
use App\Command\Credential\DeleteAll;
use App\Command\Credential\DeleteOne;
use App\Command\Credential\UpdateOne;
use App\Repository\DBCredential;
use Illuminate\Database\Query\Builder;

class CredentialTest extends AbstractUnit {
	public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(CredentialInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(CredentialValidator::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Credential(
                $repositoryMock,
                $validatorMock
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

        Credential::register($container);
        $this->assertInstanceOf(Credential::class, $container[Credential::class]);
    }

    public function testHandleCreateNew() {
    	$dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
    		->disableOriginalConstructor()
    		->setMethods([])
    		->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('Credential');

        $builderMock = $this->getMockBuilder(Builder::class)
        	->disableOriginalConstructor()
        	->getMock();

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->setMethods(['query'])
            ->getMock();
        $credentialRepository
        	->method('query')
        	->will($this->returnValue($builderMock));

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator()
        );

        $command           = new CreateNew();
        $command->name     = 'valid cred';
        $command->companyId = 1;
        $command->production = false;

        $result = $handler->handleCreateNew($command);
        $this->assertSame('valid cred', $result->name);
        $this->assertSame('valid-cred', $result->slug);
        $this->assertFalse($result->production);
        $this->assertNotEmpty($result->public);
    }

    public function testHandleUpdateOne() {
    	$dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
    		->disableOriginalConstructor()
    		->setMethods([])
    		->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('Credential');

        $builderMock = $this->getMockBuilder(Builder::class)
        	->disableOriginalConstructor()
        	->getMock();

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->setMethods(['query', 'find'])
            ->getMock();
        $credentialRepository
        	->method('query')
        	->will($this->returnValue($builderMock));
        $credentialRepository
        	->method('find')
        	->will($this->returnValue(new CredentialEntity()));

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator()
        );

        $command           = new UpdateOne();
        $command->credentialId = 0;
        $command->name = 'valid cred';

        $result = $handler->handleUpdateOne($command);
        $this->assertSame('valid cred', $result->name);
        $this->assertSame('valid-cred', $result->slug);
        $this->assertEquals(0, $result->credentialId);
    }

	public function testHandleDeleteOne() {
		$dbConnectionMock = $this->getMock('Illuminate\Database\ConnectionInterface');

        $entityFactory = new EntityFactory();
        $entityFactory->create('Credential');

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->setMethods(['delete'])
            ->getMock();
        $credentialRepository
        	->method('delete')
        	->will($this->returnValue(1));

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator()
        );

        $command           = new DeleteOne();
        $command->credentialId = 0;

        $this->assertEquals(1, $handler->handleDeleteOne($command));
	}

	public function testHandleDeleteAll() {
		$dbConnectionMock = $this->getMock('Illuminate\Database\ConnectionInterface');

        $entityFactory = new EntityFactory();
        $entityFactory->create('Credential');

        $credentialRepository = $this->getMockBuilder(DBCredential::class)
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->setMethods(['deleteByCompanyId'])
            ->getMock();
        $credentialRepository
        	->method('deleteByCompanyId')
        	->will($this->returnValue(3));

        $handler = new Credential(
            $credentialRepository,
            new CredentialValidator()
        );

        $command           = new DeleteAll();
        $command->companyId = 0;

        $this->assertEquals(3, $handler->handleDeleteAll($command));
	}

}
