<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Controller;

use App\Command\Company\Credential\CreateNew;
use App\Command\Company\Credential\DeleteAll;
use App\Command\Company\Credential\DeleteOne;
use App\Command\Company\Credential\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\Company\Credentials;
use App\Entity\Company as CompanyEntity;
use App\Entity\Company\Credential as CredentialEntity;
use App\Factory\Command;
use App\Repository\DBCredential;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class CredentialsTest extends AbstractUnit {
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

    private function getEntity() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new CredentialEntity(
            [
                'name'       => 'New Company',
                'slug'       => 'new-company',
                'public'     => 'public',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    public function testListAll() {
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

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $credentialsMock->listAll($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->getCompanyEntity(0)));
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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $credentialsMock->createNew($requestMock, $responseMock));
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
                $this->returnValue($this->getCompanyEntity(0))
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
            ->will($this->onConsecutiveCalls($this->getEntity(), $responseMock));

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

        // $this->assertEquals($responseMock, $credentialsMock->deleteAll($requestMock, $responseMock));
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
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
                    $this->getCompanyEntity(0),
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
            ->will($this->returnValue($this->getEntity()));

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

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $credentialsMock->getOne($requestMock, $responseMock));
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
                    $this->getCompanyEntity(0),
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
                    $this->getEntity(0)
                )
            );

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $credentialsMock->updateOne($requestMock, $responseMock));
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
                    $this->getCompanyEntity(0),
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
                    $this->getEntity(0)
                )
            );

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

        $credentialsMock = $this->getMockBuilder(Credentials::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $credentialsMock->deleteOne($requestMock, $responseMock));
    }
}
