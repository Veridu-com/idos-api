<?php

namespace Test\Controller;

use App\Controller\Companies;

use App\Factory\Command;
use App\Repository\CompanyInterface;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Collection;
use App\Entity\Company as EntityCompany;
use App\Repository\DBCompany;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Command\ResponseDispatch;
use App\Command\Company\CreateNew;

class CompaniesTest extends \PHPUnit_Framework_TestCase {

    public function testListAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new EntityCompany(
                        ['id' => 0]
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbCompanyMock = $this->getMockBuilder(DBCompany::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByParentId'])
            ->getMock();
        $dbCompanyMock
            ->method('getAllByParentId')
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

        $companyMock = $this->getMockBuilder(Companies::class)
            ->setConstructorArgs([$dbCompanyMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $companyMock->listAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new EntityCompany(
                        ['id' => 0]
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbCompanyMock = $this->getMockBuilder(DBCompany::class)
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

        $companyMock = $this->getMockBuilder(Companies::class)
            ->setConstructorArgs([$dbCompanyMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $companyMock->getOne($requestMock, $responseMock));
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
                    new EntityCompany(
                        ['id' => 0]
                    )
                )
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue('request'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbCompanyMock = $this->getMockBuilder(DBCompany::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByParentId'])
            ->getMock();
        $dbCompanyMock
            ->method('getAllByParentId')
            ->will($this->returnValue(new Collection(['id' => 0])));

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will(
                $this->returnValueMap(
                    [
                        [CreateNew::class, new EntityCompany()],
                        [ResponseInterface::class, $responseMock]
                    ]
                )
            );

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will(
                $this->returnValueMap(
                    [
                        'Company\\CreateNew',
                        new CreateNew()
                    ],
                    [
                        'ResponseDispatch',
                        ResponseInterface::class
                    ]
                )
            );

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $companyMock = $this->getMockBuilder(Companies::class)
            ->setConstructorArgs([$dbCompanyMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $companyMock->createNew($requestMock, $responseMock));
    }

    public function testDeleteAll() {

    }

    public function testDeleteOne() {

    }

    public function testUpdateOne() {

    }
}
