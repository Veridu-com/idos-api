<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Middleware;

use App\Entity\Company as CompanyEntity;
use App\Entity\Credential as CredentialEntity;
use App\Entity\User as UserEntity;
use App\Exception\AppException;
use App\Exception\NotFound;
use App\Factory\Entity as EntityFactory;
use App\Middleware\Auth as AuthMiddleware;
use App\Repository\DBCompany;
use App\Repository\DBCredential;
use App\Repository\DBUser;
use Jenssegers\Optimus\Optimus;
use Lcobucci\JWT;
use Slim\Http\Request;
use Test\Unit\AbstractUnit;

class AuthTest extends AbstractUnit {
    // /**
    //  * @var \Jenssengers\Optimus\Optimus
    //  */
    // private $optimus;

    // /**
    //  * @var App\Repository\DBCredential
    //  */
    // private $credentialRepositoryMock;

    // /**
    //  * @var App\Repository\DBUser
    //  */
    // private $userRepositoryMock;

    // /**
    //  * @var App\Repository\DBCompany
    //  */
    // private $companyRepositoryMock;

    // /**
    //  * @var \Lcobucci\JWT\Parser
    //  */
    // private $jwtParser;

    // /**
    //  * @var \Lcobucci\JWT\ValidationData
    //  */
    // private $jwtValidation;

    // /**
    //  * @var \Lcobucci\JWT\Signer\Hmac\Sha256
    //  */
    // private $jwtSigner;

    // /**
    //  * @var \Lcobucci\JWT\Builder
    //  */
    // private $jwtBuilder;

    // /**
    //  * @var \Slim\Http\Request
    //  */
    // private $requestMock;

    // public function setUp() {
    //     $this->optimus = $this
    //         ->getMockBuilder(Optimus::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();

    //     $dbConnectionMock = $this
    //         ->getMockBuilder('Illuminate\Database\ConnectionInterface')
    //         ->disableOriginalConstructor()
    //         ->getMock();

    //     $entityFactory = new EntityFactory($this->optimus);
    //     // $entityFactory->create('Credential');
    //     // $entityFactory->create('User');
    //     // $entityFactory->create('Company');

    //     $this->credentialRepositoryMock = $this
    //         ->getMockBuilder(DBCredential::class)
    //         ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
    //         // ->disableOriginalConstructor()
    //         ->setMethods(['findByPubKey'])
    //         ->getMock();

    //     $this->userRepositoryMock = $this
    //         ->getMockBuilder(DBUser::class)
    //         ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
    //         // ->disableOriginalConstructor()
    //         ->setMethods(['findOrCreate'])
    //         ->getMock();

    //     $this->companyRepositoryMock = $this
    //         ->getMockBuilder(DBCompany::class)
    //         ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
    //         // ->disableOriginalConstructor()
    //         ->setMethods(['findById'])
    //         ->getMock();

    //     $this->jwtParser     = new JWT\Parser();
    //     $this->jwtValidation = new JWT\ValidationData();
    //     $this->jwtSigner     = new JWT\Signer\Hmac\Sha256();

    //     $this->jwtBuilder = new JWT\Builder();

    //     $this->requestMock = new class() extends Request {
    //         protected $attributes;

    //         public function __construct() {
    //             $this->attributes = [];
    //         }

    //         public function getAttributes() {
    //             return $this->attributes;
    //         }

    //         public function withAttribute($name, $value) {
    //             $this->attributes[$name] = $value;

    //             return $this;
    //         }
    //     };
    // }

    // /**
    //  * Gets the auth middleware with the provided $authRequest.
    //  *
    //  * @param int $authRequest The auth request
    //  *
    //  * @return AuthMiddleware The auth middleware.
    //  */
    // private function getAuthMiddleware(int $authRequest) : AuthMiddleware {
    //     return new AuthMiddleware(
    //         $this->credentialRepositoryMock,
    //         $this->userRepositoryMock,
    //         $this->companyRepositoryMock,
    //         $this->jwtParser,
    //         $this->jwtValidation,
    //         $this->jwtSigner,
    //         $authRequest
    //     );
    // }

    // private function getCredentialEntity() {
    //     return new CredentialEntity(
    //         [
    //         ],
    //         $this->optimus
    //     );
    // }

    // private function generateToken(string $privateKey, array $claims) {
    //     foreach ($claims as $key => $value) {
    //         $this->jwtBuilder->set($key, $value);
    //     }

    //     return $this->jwtBuilder
    //         ->sign($this->jwtSigner, $privateKey)
    //         ->getToken();
    // }

    // public function testHandleUserTokenSuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => 'acting-user',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'companyId'  => $targetCompany->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $user = new UserEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => $targetCompany->username,
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->userRepositoryMock
    //         ->method('findOrCreate')
    //         ->willReturn($user);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($targetCompany);

    //     $claims = [
    //         'iss' => $credential->public,
    //         'sub' => $user->username
    //     ];

    //     $token = $this->generateToken($credential->private, $claims);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($user, $attributes['user']);
    //     $this->assertSame($targetCompany, $attributes['targetCompany']);
    //     $this->assertSame($credential, $attributes['credential']);
    // }

    // public function testHandleUserTokenErrorInvalidToken() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, 'jwt.invalid.token']);
    // }

    // public function testHandleUserTokenErrorCredentialNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->throwException(new NotFound()));

    //     $claims = [
    //         'iss' => 'test',
    //         'sub' => 'test'
    //     ];

    //     $token = $this->generateToken('test', $claims);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);
    // }

    // public function testHandleUserTokenErrorSignatureVerificationFailed() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => 'acting-user',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'companyId'  => $targetCompany->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $user = new UserEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => $targetCompany->username,
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->userRepositoryMock
    //         ->method('findOrCreate')
    //         ->willReturn($user);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($targetCompany);

    //     $claims = [
    //         'iss' => $credential->public,
    //         'sub' => $user->username
    //     ];

    //     $token = $this->generateToken('wrong-sign-key', $claims);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);
    // }

    // public function testHandleUserTokenErrorMissingSubClaim() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => 'acting-user',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'companyId'  => $targetCompany->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($targetCompany);

    //     $claims = [
    //         'iss' => $credential->public
    //     ];

    //     $token = $this->generateToken($credential->private, $claims);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);
    // }

    // public function testHandleUserTokenErrorActingUserNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => 'acting-user',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'companyId'  => $targetCompany->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $user = new UserEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => $targetCompany->username,
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->userRepositoryMock
    //         ->method('findOrCreate')
    //         ->will($this->throwException(new NotFound()));

    //     $claims = [
    //         'iss' => $credential->public,
    //         'sub' => $user->username
    //     ];

    //     $token = $this->generateToken($credential->private, $claims);

    //     try {
    //         $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

    //         return $this->fail('Expecting NotFound exception');
    //     } catch (NotFound $e) {
    //     }
    // }

    // public function testHandleUserTokenErrorTargetCompanyNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER);

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => 'acting-user',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'companyId'  => $targetCompany->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $user = new UserEntity(
    //         [
    //             'id'         => 1,
    //             'username'   => $targetCompany->username,
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->userRepositoryMock
    //         ->method('findOrCreate')
    //         ->willReturn($user);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->will($this->throwException(new NotFound()));

    //     $claims = [
    //         'iss' => $credential->public,
    //         'sub' => $user->username
    //     ];

    //     $token = $this->generateToken($credential->private, $claims);

    //     try {
    //         $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

    //         return $this->fail('Expecting NotFound exception');
    //     } catch (NotFound $e) {
    //     }
    // }

    // public function testHandleUserPubKeySuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PUBKEY);

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $targetUser = new UserEntity(
    //         [
    //             'id'           => 1,
    //             'credentialId' => $credential->id,
    //             'username'     => 'username-test',
    //             'created_at'   => time(),
    //             'updated_at'   => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->userRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($targetUser);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPubKey', [$this->requestMock, $credential->public]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($targetUser, $attributes['targetUser']);
    // }

    // public function testHandleUserPubKeyTargetUserNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PUBKEY);

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $targetUser = new UserEntity(
    //         [
    //             'id'           => 1,
    //             'credentialId' => $credential->id,
    //             'username'     => 'username-test',
    //             'created_at'   => time(),
    //             'updated_at'   => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->userRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->throwException(new NotFound()));

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPubKey', [$this->requestMock, $credential->public]);
    // }

    // public function testHandleUserPrivKeySuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PRIVKEY);

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $user = new UserEntity(
    //         [
    //             'id'           => 1,
    //             'credentialId' => $credential->id,
    //             'username'     => 'username-test',
    //             'created_at'   => time(),
    //             'updated_at'   => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->userRepositoryMock
    //         ->method('findByPrivKey')
    //         ->willReturn($user);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPrivKey', [$this->requestMock, $credential->private]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($user, $attributes['user']);
    // }

    // public function testHandleUserPrivKeyErrorTargetUserNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PRIVKEY);

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $user = new UserEntity(
    //         [
    //             'id'           => 1,
    //             'credentialId' => $credential->id,
    //             'username'     => 'username-test',
    //             'created_at'   => time(),
    //             'updated_at'   => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->userRepositoryMock
    //         ->method('findByPrivKey')
    //         ->will($this->throwException(new NotFound()));

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPrivKey', [$this->requestMock, $credential->private]);
    // }

    // public function testHandleCompanyPubKeySuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('public'),
    //             'private_key' => md5('private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->companyRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($company);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPubKey', [$this->requestMock, $company->public_key]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($company, $attributes['company']);
    // }

    // public function testHandleCompanyPubKeyErrorActingCompanyNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('public'),
    //             'private_key' => md5('private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->companyRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->throwException(new NotFound()));

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPubKey', [$this->requestMock, $company->public_key]);
    // }

    // public function testHandleCompanyPrivKeySuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PRIVKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('public'),
    //             'private_key' => md5('private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->companyRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($company);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPrivKey', [$this->requestMock, $company->private_key]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($company, $attributes['company']);
    // }

    // public function testHandleCompanyPrivKeyErrorActingCompanyNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PRIVKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('public'),
    //             'private_key' => md5('private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->companyRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->throwException(new NotFound()));

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPrivKey', [$this->requestMock, $company->private_key]);
    // }

    // public function testHandleCredentialTokenSuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CREDENTIAL);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('acting-public'),
    //             'private_key' => md5('acting-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'          => 2,
    //             'username'    => 'target-company',
    //             'public_key'  => md5('target-public'),
    //             'private_key' => md5('target-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $issuerCredential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Issuer Credential Test',
    //             'slug'       => 'issuer-credential-test',
    //             'public'     => md5('issuer-public'),
    //             'private'    => md5('issuer-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $subjectCredential = new CredentialEntity(
    //         [
    //             'id'         => 2,
    //             'company_id' => $targetCompany->id,
    //             'name'       => 'Subject Credential Test',
    //             'slug'       => 'subject-credential-test',
    //             'public'     => md5('subject-public'),
    //             'private'    => md5('subject-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->onConsecutiveCalls($issuerCredential, $subjectCredential));

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->will($this->onConsecutiveCalls($company, $targetCompany));

    //     $claims = [
    //         'iss' => $issuerCredential->public,
    //         'sub' => $subjectCredential->public
    //     ];

    //     $token = $this->generateToken($issuerCredential->private, $claims);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleHandlerToken', [$this->requestMock, $token]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($company, $attributes['company']);
    //     $this->assertSame($targetCompany, $attributes['targetCompany']);
    //     $this->assertSame($subjectCredential, $attributes['credential']);
    // }

    // public function testHandleCredentialTokenErrorInvalidToken() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CREDENTIAL);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleHandlerToken', [$this->requestMock, 'invalid.token']);
    // }

    // public function testHandleCredentialTokenErrorIssuerCredentialNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CREDENTIAL);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('acting-public'),
    //             'private_key' => md5('acting-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'          => 2,
    //             'username'    => 'target-company',
    //             'public_key'  => md5('target-public'),
    //             'private_key' => md5('target-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $issuerCredential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Issuer Credential Test',
    //             'slug'       => 'issuer-credential-test',
    //             'public'     => md5('issuer-public'),
    //             'private'    => md5('issuer-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $subjectCredential = new CredentialEntity(
    //         [
    //             'id'         => 2,
    //             'company_id' => $targetCompany->id,
    //             'name'       => 'Subject Credential Test',
    //             'slug'       => 'subject-credential-test',
    //             'public'     => md5('subject-public'),
    //             'private'    => md5('subject-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->throwException(new NotFound()));

    //     $claims = [
    //         'iss' => $issuerCredential->public,
    //         'sub' => $subjectCredential->public
    //     ];

    //     $token = $this->generateToken($issuerCredential->private, $claims);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleHandlerToken', [$this->requestMock, $token]);
    // }

    // public function testHandleCredentialTokenErrorMissingSub() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CREDENTIAL);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('acting-public'),
    //             'private_key' => md5('acting-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'          => 2,
    //             'username'    => 'target-company',
    //             'public_key'  => md5('target-public'),
    //             'private_key' => md5('target-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $issuerCredential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Issuer Credential Test',
    //             'slug'       => 'issuer-credential-test',
    //             'public'     => md5('issuer-public'),
    //             'private'    => md5('issuer-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $subjectCredential = new CredentialEntity(
    //         [
    //             'id'         => 2,
    //             'company_id' => $targetCompany->id,
    //             'name'       => 'Subject Credential Test',
    //             'slug'       => 'subject-credential-test',
    //             'public'     => md5('subject-public'),
    //             'private'    => md5('subject-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->onConsecutiveCalls($issuerCredential, $subjectCredential));

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->will($this->onConsecutiveCalls($company, $targetCompany));

    //     $claims = [
    //         'iss' => $issuerCredential->public
    //     ];

    //     $token = $this->generateToken($issuerCredential->private, $claims);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleHandlerToken', [$this->requestMock, $token]);
    // }

    // public function testHandleCredentialTokenErrorActingCompanyNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CREDENTIAL);

    //     $company = new CompanyEntity(
    //         [
    //             'id'          => 1,
    //             'username'    => 'acting-company',
    //             'public_key'  => md5('acting-public'),
    //             'private_key' => md5('acting-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $targetCompany = new CompanyEntity(
    //         [
    //             'id'          => 2,
    //             'username'    => 'target-company',
    //             'public_key'  => md5('target-public'),
    //             'private_key' => md5('target-private'),
    //             'created_at'  => time(),
    //             'updated_at'  => time()
    //         ],
    //         $this->optimus
    //     );

    //     $issuerCredential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Issuer Credential Test',
    //             'slug'       => 'issuer-credential-test',
    //             'public'     => md5('issuer-public'),
    //             'private'    => md5('issuer-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $subjectCredential = new CredentialEntity(
    //         [
    //             'id'         => 2,
    //             'company_id' => $targetCompany->id,
    //             'name'       => 'Subject Credential Test',
    //             'slug'       => 'subject-credential-test',
    //             'public'     => md5('subject-public'),
    //             'private'    => md5('subject-private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->onConsecutiveCalls($issuerCredential, $subjectCredential));

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->will($this->throwException(new NotFound()));

    //     $claims = [
    //         'iss' => $issuerCredential->public,
    //         'sub' => $subjectCredential->public
    //     ];

    //     $token = $this->generateToken($issuerCredential->private, $claims);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleHandlerToken', [$this->requestMock, $token]);
    // }

    // public function testHandleCredentialPubKeySuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Company Test',
    //             'slug'       => 'company-test',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($company);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->public]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($company, $attributes['company']);
    //     $this->assertSame($credential, $attributes['credential']);
    // }

    // public function testHandleCredentialPubKeyErrorCredentialNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Company Test',
    //             'slug'       => 'company-test',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->will($this->throwException(new NotFound()));

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($company);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->public]);
    // }

    // public function testHandleCredentialPubKeyErrorActingCompanyNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Company Test',
    //             'slug'       => 'company-test',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPubKey')
    //         ->willReturn($credential);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->will($this->throwException(new NotFound()));

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->public]);
    // }

    // public function testHandleCredentialPrivKeySuccess() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PRIVKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Company Test',
    //             'slug'       => 'company-test',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPrivKey')
    //         ->willReturn($credential);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($company);

    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->private]);

    //     $attributes = $this->requestMock->getAttributes();

    //     $this->assertSame($company, $attributes['company']);
    //     $this->assertSame($credential, $attributes['credential']);
    // }

    // public function testHandleCredentialPrivKeyErrorCredentialNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Company Test',
    //             'slug'       => 'company-test',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPrivKey')
    //         ->will($this->throwException(new NotFound()));

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->willReturn($company);

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPrivKey', [$this->requestMock, $credential->public]);
    // }

    // public function testHandleCredentialPrivKeyErrorActingCompanyNotFound() {
    //     $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

    //     $company = new CompanyEntity(
    //         [
    //             'id'         => 1,
    //             'name'       => 'Company Test',
    //             'slug'       => 'company-test',
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $credential = new CredentialEntity(
    //         [
    //             'id'         => 1,
    //             'company_id' => $company->id,
    //             'name'       => 'Credential Test',
    //             'slug'       => 'credential-test',
    //             'public'     => md5('public'),
    //             'private'    => md5('private'),
    //             'created_at' => time(),
    //             'updated_at' => time()
    //         ],
    //         $this->optimus
    //     );

    //     $this->credentialRepositoryMock
    //         ->method('findByPrivKey')
    //         ->willReturn($credential);

    //     $this->companyRepositoryMock
    //         ->method('findById')
    //         ->will($this->throwException(new NotFound()));

    //     $this->setExpectedException(AppException::class);
    //     $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPrivKey', [$this->requestMock, $credential->public]);
    // }
}
