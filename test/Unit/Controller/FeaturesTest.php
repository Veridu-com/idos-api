<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Controller;

use App\Command\Feature\CreateNew;
use App\Command\Feature\DeleteAll;
use App\Command\Feature\DeleteOne;
use App\Command\Feature\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\Features;
use App\Entity\Feature as FeatureEntity;
use App\Entity\User;
use App\Factory\Command;
use App\Repository\DBFeature;
use App\Repository\DBUser;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class FeaturesTest extends AbstractUnit {
    private function getEntity($id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new FeatureEntity(
            [
                'name'       => 'New Feature',
                'id'         => $id,
                'slug'       => 'new-feature',
                'created_at' => time(),
                'updated_at' => null
            ],
            $optimus
        );
    }

    public function testListAll() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $userId = 1;
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => $userId], $optimus
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByUserId'])
            ->getMock();

        $repositoryMock
            ->method('getAllByUserId')
            ->will(
                $this->returnValue(
                    [
                        'pagination' => 'pagination',
                        'collection' => new Collection(
                            new FeatureEntity(
                                [
                                        'name'       => 'name',
                                        'slug'       => 'slug',
                                        'value'      => 'value',
                                        'user_id'    => $userId,
                                        'created_at' => time(),
                                        'updated_at' => null
                                    ],
                                $optimus
                            )
                        )
                    ]
                )
            );

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $userRepositoryMock
            ->method('find')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => $userId], $optimus
                    )
                )
            );

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

        $featuresMock = $this->getMockBuilder(Features::class)
            ->setConstructorArgs([$repositoryMock, $userRepositoryMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $featuresMock->listAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $userId      = 1;
        $featureSlug = 'friend-count';
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new User(
                        ['id' => $userId], $optimus
                    ),
                    $featureSlug
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->disableOriginalConstructor()
            ->setMethods(['findByUserIdAndSlug'])
            ->getMock();

        $repositoryMock
            ->method('findByUserIdAndSlug')
            ->will(
                $this->returnValue(
                    new FeatureEntity(
                        [
                            'name'       => 'name',
                            'slug'       => $featureSlug,
                            'value'      => 'value',
                            'user_id'    => $userId,
                            'created_at' => time(),
                            'updated_at' => null
                        ],
                        $optimus
                    )
                )
            );

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $userRepositoryMock
            ->method('find')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => $userId], $optimus
                    )
                )
            );

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

        $featuresMock = $this->getMockBuilder(Features::class)
            ->setConstructorArgs([$repositoryMock, $userRepositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $featuresMock->getOne($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();

        $userId = 1;
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => $userId], $optimus
                    )
                )
            );

        $requestMock
            ->method('getParsedBody')
            ->will($this->returnValue(['request' => 'request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $userRepositoryMock
            ->method('find')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => $userId], $optimus
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
            ->will($this->onConsecutiveCalls($this->getEntity(1), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new CreateNew(), new ResponseDispatch()));

        $featuresMock = $this->getMockBuilder(Features::class)
            ->setConstructorArgs([$repositoryMock, $userRepositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $featuresMock->createNew($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => 1], $optimus
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->disableOriginalConstructor()
            ->setMethods(['deleteByUserId'])
            ->getMock();

        $amountDeleted = 10;
        $repositoryMock
            ->method('deleteByUserId')
            ->will(
                $this->returnValue(
                    $amountDeleted
                )
            );

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $userRepositoryMock
            ->method('find')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => 1], $optimus
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
            ->will($this->onConsecutiveCalls($amountDeleted, $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteAll(), new ResponseDispatch()));

        $featuresMock = $this->getMockBuilder(Features::class)
            ->setConstructorArgs([$repositoryMock, $userRepositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $featuresMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();

        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->returnValue(
                        new User(
                            ['id' => 1], $optimus
                        )
                    ),
                    'slug'
                )
            );

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $userRepositoryMock
            ->method('find')
            ->will(
                $this->returnValue(
                    new User(
                        ['id' => 1], $optimus
                    )
                )
            );

        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();

        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls(1, $responseMock));

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

        $features = $this->getMockBuilder(Features::class)
            ->setConstructorArgs([$repositoryMock, $userRepositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $features->deleteOne($requestMock, $responseMock));
    }

    public function testUpdateOne() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();

        $userId      = 1;
        $featureSlug = 'slug';
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new User(
                        ['id' => $userId],
                        $optimus
                    ),
                    $featureSlug
                )
            );

        $requestMock
            ->method('getParsedBody')
            ->will($this->returnValue(['request' => 'request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->disableOriginalConstructor()
            ->setMethods(['findByUserIdAndSlug'])
            ->getMock();

        $repositoryMock
            ->method('findByUserIdAndSlug')
            ->will(
                $this->returnValue(
                    new FeatureEntity(
                        [
                           'name'       => 'name',
                           'slug'       => $featureSlug,
                           'value'      => 'value',
                           'user_id'    => $userId,
                           'created_at' => time(),
                           'updated_at' => null
                        ],
                        $optimus
                    )
                )
            );

        $userRepositoryMock = $this->getMockBuilder(DBUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();

        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will(
                $this->onConsecutiveCalls(
                    new FeatureEntity(
                        [
                            'name'       => 'name',
                            'slug'       => $featureSlug,
                            'value'      => 'value',
                            'user_id'    => $userId,
                            'created_at' => time(),
                            'updated_at' => null
                        ],
                        $optimus
                    ),
                    $responseMock
                )
            );

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new UpdateOne(), new ResponseDispatch()));

        $featuresMock = $this->getMockBuilder(Features::class)
            ->setConstructorArgs([$repositoryMock, $userRepositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $featuresMock->updateOne($requestMock, $responseMock));
    }
}
