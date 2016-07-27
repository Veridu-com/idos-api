<?php

namespace Test\Unit\Controller;

use App\Command\Credential\CreateNew;
use App\Command\Credential\DeleteAll;
use App\Command\Credential\DeleteOne;
use App\Command\Credential\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\Credentials;
use App\Entity\Company;
use App\Entity\Credential as CredentialEntity;
use App\Factory\Command;
use App\Repository\DBCredential;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class CreentialsTest extends AbstractUnit {
    public function testListAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new Company(
                        ['id' => 0]
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBCredential::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $repositoryMock
            ->method('getAllByCompanyId')
            ->will($this->returnValue(new Collection(['id' => 0])));

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $credentialsMock->listAll($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new Company(
                        ['id' => 0]
                    )
                )
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBCredential::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls(new CredentialEntity(), $responseMock));

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $credentialsMock->createNew($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new Company(
                        ['id' => 0]
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBCredential::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls(new CredentialEntity(), $responseMock));

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $credentialsMock->deleteAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new Company(
                        ['id' => 0]
                    ),
                    'publickey'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setMethods(['findByPubKey'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock
            ->method('findByPubKey')
            ->will(
                $this->returnValue(
                    new CredentialEntity(
                        ['updated_at' => 'date']
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $credentialsMock->getOne($requestMock, $responseMock));
    }

    public function testUpdateOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new Company(
                        ['id' => 0]
                    ),
                    'publickey'
                )
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['id' => 'id']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setMethods(['findByPubKey'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock
            ->method('findByPubKey')
            ->will(
                $this->returnValue(
                    new CredentialEntity(
                        [
                            'id'         => 0,
                            'updated_at' => 'date'
                        ]
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls(new CredentialEntity(), $responseMock));

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $credentialsMock->updateOne($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new Company(
                        ['id' => 0]
                    ),
                    'publickey'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBCredential::class)
            ->setMethods(['findByPubKey'])
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryMock
            ->method('findByPubKey')
            ->will(
                $this->returnValue(
                    new CredentialEntity(
                        [
                            'id'         => 0,
                            'updated_at' => 'date'
                        ]
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls(new CredentialEntity(), $responseMock));

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $credentialsMock->deleteOne($requestMock, $responseMock));
    }
}
