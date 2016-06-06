<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Exception\NotFound;
use App\Model\Company;
use App\Repository\AbstractDBRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AbstractDBRepositoryTest extends \PHPUnit_Framework_TestCase {
    public function testFindNotFound() {
        $modelMock = $this->getMockBuilder(Company::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOrFail'])
            ->getMock();
        $modelMock
            ->method('findOrFail')
            ->will($this->throwException(new ModelNotFoundException(CompanyModel::class)));

        $this->setExpectedException(NotFound::class);

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setMethods(null)
            ->setConstructorArgs([$modelMock])
            ->getMockForAbstractClass();
        $abstractMock->find(0);
    }

    public function testFindByKeyNotFound() {
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

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setMethods(null)
            ->setConstructorArgs([$modelMock])
            ->getMockForAbstractClass();
        $abstractMock->findByKey('', '');
    }
}
