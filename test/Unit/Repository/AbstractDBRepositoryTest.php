<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Repository;

use App\Entity\Company as CompanyEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\AbstractDBRepository;
use App\Repository\RepositoryInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class AbstractDBRepositoryTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getEntity($id) {
        return new CompanyEntity(
            [
                'name'       => 'New Company',
                'id'         => $id,
                'slug'       => 'new-company',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    public function testGetTableNameRuntimeException() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $getTableName = $this->setProtectedMethod($abstractMock, 'getTableName');
        $this->setExpectedException(\RuntimeException::class);
        $getTableName->invoke($abstractMock);
    }

    public function testGetTableName() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setProtectedProperty($abstractMock, 'tableName', 'AbstractDBRepository');
        $getTableName = $this->setProtectedMethod($abstractMock, 'getTableName');
        $this->assertSame('AbstractDBRepository', $getTableName->invoke($abstractMock));

    }

    public function testConstructorRightInterface() {
        $entityFactory    = new Entity($this->optimus);
        $dbConnectionMock = $this
            ->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $abstractDBMock = $this
            ->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $this->assertInstanceOf(RepositoryInterface::class, $abstractDBMock);
    }

    public function testSaveThrowsException() {
        $entityFactory = new Entity($this->optimus);

        $dbConnectionMock = $this
            ->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'update'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('update')
            ->will($this->returnValue(true));

        $abstractDBMock = $this
            ->getMockBuilder(AbstractDBRepository::class)
            ->setMethods(['query'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMockForAbstractClass();
        $abstractDBMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->setExpectedException(\Exception::class);
        $entity = $this->getEntity(1);
        $abstractDBMock->save($entity);
    }

    public function testSaveEmptyId() {
        $entityFactory = new Entity($this->optimus);

        $dbConnectionMock = $this
            ->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['insertGetId'])
            ->getMock();
        $queryMock
            ->method('insertGetId')
            ->will($this->returnValue($queryMock));

        $abstractDBMock = $this
            ->getMockBuilder(AbstractDBRepository::class)
            ->setMethods(['query', 'create'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMockForAbstractClass();
        $abstractDBMock
            ->method('query')
            ->will($this->returnValue($queryMock));
        $abstractDBMock
            ->method('create')
            ->will($this->returnValue($this->getEntity(1)));

        $entity = $this->getEntity('');
        $this->assertInstanceOf(CompanyEntity::class, $abstractDBMock->save($entity));

        // assertEquals: we want the properties to be the same but not to reference the same object
        $this->assertSame($this->getEntity(1), $abstractDBMock->save($entity));
    }

    public function testGetEntityNameRuntimeException() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $getEntityName = $this->setProtectedMethod($abstractMock, 'getEntityName');
        $this->setExpectedException(\RuntimeException::class);
        $getEntityName->invoke($abstractMock);
    }

    public function testGetEntityName() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setProtectedProperty($abstractMock, 'entityName', 'Entity');
        $getEntityName = $this->setProtectedMethod($abstractMock, 'getEntityName');
        $this->assertSame('Entity', $getEntityName->invoke($abstractMock));
    }

    public function testGetEntityClassNameRuntimeException() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $getEntityClassName = $this->setProtectedMethod($abstractMock, 'getEntityClassName');
        $this->setExpectedException(\RuntimeException::class);
        $getEntityClassName->invoke($abstractMock);
    }

    public function testGetEntityClassName() {
        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntityName'])
            ->getMock();
        $abstractMock
            ->method('getEntityName')
            ->will($this->returnValue('Entity'));

        $getEntityClassName = $this->setProtectedMethod($abstractMock, 'getEntityClassName');
        $this->assertSame('\App\Entity\Entity', $getEntityClassName->invoke($abstractMock));
    }

    public function testFindNotFound() {
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $queryMock
            ->method('find')
            ->will($this->returnValue(''));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $this->optimus, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->setExpectedException(NotFound::class);
        $abstractMock->find(0);
    }

    public function testFind() {
        $array = [
            'name'       => 'AbstractDBCompany',
            'slug'       => 'slug',
            'public_key' => 'public_key',
            'created_at' => time(),
            'updated_at' => time()
        ];

        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $queryMock
            ->method('find')
            ->will($this->returnValue(new CompanyEntity($array, $this->optimus)));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $this->optimus, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->assertSame($array, $abstractMock->find(0)->toArray());
    }

    public function testDeleteByEmptyConstraintsException() {
        $entityFactory    = new Entity($this->optimus);
        $dbConnectionMock = $this
            ->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $abstractDBMock = $this
            ->getMockBuilder(AbstractDBRepository::class)
            ->setMethods(null)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMockForAbstractClass();

        $this->setExpectedException(\RuntimeException::class);
        $abstractDBMock->deleteBy([]);
    }

    public function testDeleteBy() {
        $entityFactory    = new Entity($this->optimus);
        $dbConnectionMock = $this
            ->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $queryMock = $this
            ->getMockBuilder(Builder::class)
            ->setMethods(['delete'])
            ->disableOriginalConstructor()
            ->getMock();
        $queryMock
            ->method('delete')
            ->will($this->returnValue(1));

        $abstractDBMock = $this
            ->getMockBuilder(AbstractDBRepository::class)
            ->setMethods(['query'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMockForAbstractClass();
        $abstractDBMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $this->assertSame(1, $abstractDBMock->deleteBy(['id' => 0]));
    }

    public function testFindByKeyNotFound() {
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([])));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $this->optimus, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $findBy = $this->setProtectedMethod($abstractMock, 'findBy');
        $this->assertEmpty($findBy->invoke($abstractMock, ['key' => 'value'])->toArray());
    }

    public function testFindBy() {
        $array = [
            'name'       => 'AbstractDBCompany',
            'slug'       => 'slug',
            'public_key' => 'public_key',
            'created_at' => time(),
            'updated_at' => time()
        ];
        $entityMock = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection(new CompanyEntity($array, $this->optimus))));

        $abstractMock = $this->getMockBuilder(AbstractDBRepository::class)
            ->setConstructorArgs([$entityMock, $this->optimus, $connectionMock])
            ->setMethods(['query'])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('query')
            ->will($this->returnValue($queryMock));

        $findBy = $this->setProtectedMethod($abstractMock, 'findBy');
        $this->assertSame($array, $findBy->invoke($abstractMock, ['key' => 'value'])->toArray());
    }
}
