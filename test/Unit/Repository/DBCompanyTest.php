<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Exception\NotFound;
use App\Model\Company;
use App\Repository\DBCompany;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DBCompanyTest extends \PHPUnit_Framework_TestCase {
    public function testFindBySlugNotFound() {
        $modelMock = $this->getMockBuilder(Company::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'firstOrFail'])
            ->getMock();
        $modelMock
            ->method('where')
            ->willReturn($modelMock);
        $modelMock
            ->method('firstOrFail')
            ->will($this->throwException(new ModelNotFoundException(CompanyModel::class)));

        $this->setExpectedException(NotFound::class);
        $repository = new DBCompany($modelMock);
        $repository->findBySlug('');
    }

    public function testFindByPubKeyNotFound() {
        $modelMock = $this->getMockBuilder(Company::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'firstOrFail'])
            ->getMock();
        $modelMock
            ->method('where')
            ->willReturn($modelMock);
        $modelMock
            ->method('firstOrFail')
            ->will($this->throwException(new ModelNotFoundException(CompanyModel::class)));

        $this->setExpectedException(NotFound::class);
        $repository = new DBCompany($modelMock);
        $repository->findByPubKey('');
    }

    public function testFindByPrivKeyNotFound() {
        $modelMock = $this->getMockBuilder(Company::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'firstOrFail'])
            ->getMock();
        $modelMock
            ->method('where')
            ->willReturn($modelMock);
        $modelMock
            ->method('firstOrFail')
            ->will($this->throwException(new ModelNotFoundException(CompanyModel::class)));

        $this->setExpectedException(NotFound::class);
        $repository = new DBCompany($modelMock);
        $repository->findByPrivKey('');
    }
}
