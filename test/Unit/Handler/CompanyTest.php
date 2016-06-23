<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Handler;

use App\Command\CompanyCreateNew;
use App\Command\CompanyDeleteOne;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Company;
use App\Model\Company as CompanyModel;
use App\Repository\CompanyInterface;
use App\Repository\DBCompany;
use App\Validator\Company as CompanyValidator;
use Slim\Container;

class CompanyTest extends \PHPUnit_Framework_TestCase {
    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(CompanyValidator::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Company(
                $repositoryMock,
                $validatorMock
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

        $container['repositoryFactory'] = function () use ($repositoryFactoryMock) {
            return $repositoryFactoryMock;
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

        Company::register($container);
        $this->assertInstanceOf(Company::class, $container[Company::class]);
    }

    public function testHandleCompanyCreateNewInvalidCompanyName() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $handler = new Company(
            $repositoryMock,
            new CompanyValidator()
        );
        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CompanyCreateNew::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->name = '';

        $handler->handleCompanyCreateNew($commandMock);
    }

    public function testHandleCompanyCreateNew() {

        $companyRepository = $this->getMockBuilder(DBCompany::class)
            ->setMethods(['save'])
            ->setConstructorArgs([new CompanyModel()])
            ->getMock();
        $companyRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $handler = new Company(
            $companyRepository,
            new CompanyValidator()
        );

        $command           = new CompanyCreateNew();
        $command->name     = 'valid co';
        $command->parentId = 1;

        $result = $handler->handleCompanyCreateNew($command);
        $this->assertSame('valid co', $result['name']);
        $this->assertSame('valid-co', $result['slug']);
        $this->assertNotEmpty($result['public_key']);
    }

    public function testHandleCompanyDeleteOneInvalidCompanySlug() {
        $repositoryMock = $this
            ->getMockBuilder(CompanyInterface::class)
            ->getMock();

        $handler = new Company(
            $repositoryMock,
            new CompanyValidator()
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CompanyDeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->companySlug = '';

        $handler->handleCompanyDeleteOne($commandMock);
    }
}
