<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use App\Entity\Company as CompanyEntity;
use App\Entity\Role as RoleEntity;
use App\Entity\RoleAccess as RoleAccessEntity;
use App\Entity\User as UserEntity;
use App\Exception\NotAllowed as NotAllowedException;
use App\Factory\Entity as EntityFactory;
use App\Middleware\UserPermission;
use App\Repository\DBRoleAccess;
use App\Repository\RoleAccessInterface;
use Jenssegers\Optimus\Optimus;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\RouteInterface;
use Test\Unit\AbstractUnit;

/**
 * UserPermission middleware tests
 * The idea is to test all possible combinations of a company or a user acessing a route that requires
 * a specific permission. A test for every combination of the existing permissions (NONE, READ, WRITE,
 * EXECUTE) in the targetUser and in the route is made.
 */
class UserPermissionTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getUserEntity() {
        return new UserEntity(
            [
                'id'         => 1,
                'username'   => 'userName',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $this->optimus
        );
    }

    /**
     * Mocks the necessary classes and methods used in the UserPermission middleware.
     */
    public function mockBasic(&$dbConnectionMock, &$entityFactory, &$routeMock, &$requestMock, &$responseMock, &$nextMock) {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();
        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('RoleAccess');
        $routeMock = $this->getMockBuilder(RouteInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();
        $routeMock
            ->method('getName')
            ->will($this->returnValue(''));
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->setMethods(['getName', 'withHeader'])
            ->getMock();
        $responseMock
            ->method('getName')
            ->will($this->returnValue(''));
        $responseMock
            ->method('withHeader')
            ->will($this->returnSelf());
        $nextMock = function ($request, $response) {
            return $response;
        };
    }

    /**
     * Mocks the DBRoleAccess repository for different returns of the method findByIdentityRoleResource
     * according to what combination of permissions we are testing for.
     *
     * @param mixed $dbConnectionMock         The $dbConnectionMock from mockBasic method.
     * @param mixed $entityFactory            The $entityFactory from mockBasic method.
     * @param mixed $roleAccessRepositoryMock The DBRoleAccess mock reference that is used later.
     * @param array $config                   An array containing the permissions we are giving for actingCompany/actingUser
     *                                        to access the targetUser.
     */
    public function roleAccessRepositoryMockConfig($dbConnectionMock, $entityFactory, &$roleAccessRepositoryMock, array $config) {
        $roleAccessRepositoryMock = $this
            ->getMockBuilder(DBRoleAccess::class)
            ->setMethods(['findByIdentityRoleResource'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->disableOriginalConstructor()
            ->getMock();
        $roleAccessRepositoryMock
            ->method('findByIdentityRoleResource')
            ->will($this->returnValueMap([
                [1, RoleEntity::COMPANY, 'test-resource', new RoleAccessEntity([
                    'role'     => RoleEntity::COMPANY,
                    'resource' => 'test-resource',
                    'access'   => $config[RoleEntity::COMPANY]],
                    $this->optimus
                )],
                [1, RoleEntity::USER, 'test-resource', new RoleAccessEntity([
                    'role'     => RoleEntity::USER,
                    'resource' => 'test-resource',
                    'access'   => $config[RoleEntity::USER]],
                    $this->optimus
                )]
            ]));
    }

    public function testConstructCorrectInterface() {
        $roleAccessRepositoryMock = $this
            ->getMockBuilder(RoleAccessInterface::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Middleware\\MiddlewareInterface',
            new UserPermission(
                $roleAccessRepositoryMock,
                'test-resource',
                RoleAccessEntity::ACCESS_WRITE
            )
        );
    }

    /**
     * Test that we cannot have an actingCompany and an actingUser defined at the same time.
     */
    public function testActingCompanyAndActingUserException() {
        $this->mockBasic($dbConnectionMock, $entityFactory, $routeMock, $requestMock, $responseMock, $nextMock);
        $this->roleAccessRepositoryMockConfig($dbConnectionMock, $entityFactory, $roleAccessRepositoryMock, [
                RoleEntity::COMPANY => [
                    RoleAccessEntity::ACCESS_READ
                ],
                RoleEntity::USER => [
                    RoleAccessEntity::ACCESS_READ
                ]
            ]);

        $userPermissionMiddleware = new UserPermission(
            $roleAccessRepositoryMock,
            'test-resource',
            RoleAccessEntity::ACCESS_READ
        );

        $requestMock
            ->expects($this->exactly(4))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new UserEntity(
                    [
                        'id'         => 1,
                        'username'   => 'acting-username',
                        'identityId' => 1,
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus),
                    new UserEntity(
                    [
                        'id'         => 2,
                        'username'   => 'target-username',
                        'identityId' => 2,
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus),
                    new CompanyEntity(
                    [
                        'name'       => 'New Company',
                        'id'         => 1,
                        'slug'       => 'acting-company',
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus),
                    $routeMock
                )
            );

        try {
            $userPermissionMiddleware($requestMock, $responseMock, $nextMock);

            return $this->fail('Expected \RuntimeException');
        } catch(\RuntimeException $e) {

        }
    }

    /**
     * Now we have a DBRoleAccess mock configured with access level $actingAccessLevel for a given role,
     * we create a route with access level $routeAccessLevel and test if it throws or not a NotAllowed
     * exception according to $shouldPass.
     */
    public function doTestRouteWithAccessLevel($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, int $routeAccessLevel, int $actingAccessLevel, bool $shouldPass) {
        $userPermissionMiddleware = new UserPermission(
            $roleAccessRepositoryMock,
            'test-resource',
            $routeAccessLevel
        );

        //echo "Testing route with routeAccessLevel = $routeAccessLevel; actingAccessLevel = $actingAccessLevel; shouldPass = $shouldPass\n";

        try {
            $userPermissionMiddleware($requestMock, $responseMock, $nextMock);

            if(! $shouldPass) {
                return $this->fail("Expected Exception\NotAllowed; routeAccessLevel: $routeAccessLevel; actingAccessLevel: $actingAccessLevel");
            }
        } catch(NotAllowedException $e) {
            if($shouldPass) {
                return $this->fail("Not Expecting Exception\NotAllowed; routeAccessLevel: $routeAccessLevel; actingAccessLevel: $actingAccessLevel");
            }
        }
    }

    /**
     * Generate all possible permission combinations for routes.
     */
    public function doTestRouteWithAccessLevelCombinations($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, int $actingAccessLevel) {

        $possibleAccessLevels = [
            RoleAccessEntity::ACCESS_NONE,
            RoleAccessEntity::ACCESS_READ,
            RoleAccessEntity::ACCESS_WRITE,
            RoleAccessEntity::ACCESS_EXECUTE
        ];

        //We have 4 possible access levels, so we need 4 nested loops to archieve all possible combinations
        foreach($possibleAccessLevels as $accessLevel1) {
            $routeAccessLevel = $accessLevel1;
            $shouldPass       = ($routeAccessLevel & $actingAccessLevel) == $routeAccessLevel;
            $this->doTestRouteWithAccessLevel($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingAccessLevel, $shouldPass);

            foreach($possibleAccessLevels as $accessLevel2) {
                //We can sum the access levels since their definition in Entity\RoleAccess are not bit-colliding
                $routeAccessLevel = $accessLevel1 + $accessLevel2;

                //We should pass the route if we have (at least) the required permissions to access it
                $shouldPass = ($routeAccessLevel & $actingAccessLevel) == $routeAccessLevel;
                $this->doTestRouteWithAccessLevel($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingAccessLevel, $shouldPass);

                foreach($possibleAccessLevels as $accessLevel3) {
                    $routeAccessLevel = $accessLevel1 + $accessLevel2 + $accessLevel3;
                    $shouldPass       = ($routeAccessLevel & $actingAccessLevel) == $routeAccessLevel;
                    $this->doTestRouteWithAccessLevel($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingAccessLevel, $shouldPass);

                    foreach($possibleAccessLevels as $accessLevel4) {
                        $routeAccessLevel = $accessLevel1 + $accessLevel2 + $accessLevel3 + $accessLevel4;
                        $shouldPass       = ($routeAccessLevel & $actingAccessLevel) == $routeAccessLevel;
                        $this->doTestRouteWithAccessLevel($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingAccessLevel, $shouldPass);

                    }
                }
            }
        }
    }

    /**
     * Configure the DBRoleAccess mock to assign access level $actingAccessLevel to role $actingRole
     * and then, given this configuration, start generating all possible combinations of route permissions.
     */
    public function doTestWithAccessLevel($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, $actingAccessLevel, $actingRole) {
        $this->roleAccessRepositoryMockConfig($dbConnectionMock, $entityFactory, $roleAccessRepositoryMock, [
            RoleEntity::COMPANY => ($actingRole === RoleEntity::COMPANY) ? $actingAccessLevel : RoleAccessEntity::ACCESS_NONE,
            RoleEntity::USER    => ($actingRole === RoleEntity::USER) ? $actingAccessLevel : RoleAccessEntity::ACCESS_NONE
        ]);

        $this->doTestRouteWithAccessLevelCombinations($roleAccessRepositoryMock, $requestMock, $responseMock, $nextMock, $actingAccessLevel);
    }

    /**
     * Define all possible permission combinations for the role $actingRole in the DBRoleAccess.
     */
    public function doTestWithAccessLevelCombinations($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, $actingRole) {

        $possibleAccessLevels = [
            RoleAccessEntity::ACCESS_NONE,
            RoleAccessEntity::ACCESS_READ,
            RoleAccessEntity::ACCESS_WRITE,
            RoleAccessEntity::ACCESS_EXECUTE
        ];

        //We have 4 possible access levels, so we need 4 nested loops to archieve all possible combinations
        foreach($possibleAccessLevels as $accessLevel1) {
            $routeAccessLevel = $accessLevel1;
            $this->doTestWithAccessLevel($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingRole);

            foreach($possibleAccessLevels as $accessLevel2) {
                //We can sum the access levels since their definition in Entity\RoleAccess are not bit-colliding
                $routeAccessLevel = $accessLevel1 + $accessLevel2;
                $this->doTestWithAccessLevel($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingRole);

                foreach($possibleAccessLevels as $accessLevel3) {
                    $routeAccessLevel = $accessLevel1 + $accessLevel2 + $accessLevel3;
                    $this->doTestWithAccessLevel($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingRole);

                    foreach($possibleAccessLevels as $accessLevel4) {
                        $routeAccessLevel = $accessLevel1 + $accessLevel2 + $accessLevel3 + $accessLevel4;
                        $this->doTestWithAccessLevel($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, $routeAccessLevel, $actingRole);

                    }
                }
            }
        }
    }

    /**
     * Tests all possible permission combination of a company acessing a route.
     */
    public function testCompanyAccess() {

        $this->mockBasic($dbConnectionMock, $entityFactory, $routeMock, $requestMock, $responseMock, $nextMock);

        //A company is acessing a route, so an actingUser should not be defined
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValueMap([
                    ['actingUser', null, null], //acting-user
                    ['targetUser', null, new UserEntity(
                    [
                        'id'         => 1,
                        'username'   => 'target-username',
                        'identityId' => 1,
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus)],
                    ['actingCompany', null, new CompanyEntity(
                    [
                        'name'       => 'New Company',
                        'id'         => 1,
                        'slug'       => 'acting-company',
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus)],
                    ['route', null, $routeMock]
                ])
            );

        //Tests all possible combination for role RoleEntity::COMPANY
        $this->doTestWithAccessLevelCombinations($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, RoleEntity::COMPANY);
    }

    /**
     * Tests all possible permission combination of a user acessing a route.
     */
    public function testUserAccess() {

        $this->mockBasic($dbConnectionMock, $entityFactory, $routeMock, $requestMock, $responseMock, $nextMock);

        //A user is acessing a route, so an actingCompany should not be defined
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValueMap([
                    ['actingUser', null, new UserEntity(
                    [
                        'id'         => 2,
                        'username'   => 'acting-username',
                        'identityId' => 2,
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus)], //acting-user
                    ['targetUser', null, new UserEntity(
                    [
                        'id'         => 1,
                        'username'   => 'target-username',
                        'identityId' => 1,
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus)],
                    ['actingCompany', null, null],
                    ['route', null, $routeMock]
                ])
            );

        //Tests all possible combination for role RoleEntity::COMPANY
        $this->doTestWithAccessLevelCombinations($dbConnectionMock, $entityFactory, $requestMock, $responseMock, $nextMock, RoleEntity::USER);
    }
}
