<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

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
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    /**
     * App\Repository\DBCredential $credentialRepositoryMock.
     */
    private $credentialRepositoryMock;

    /**
     * App\Repository\DBUser $userRepositoryMock.
     */
    private $userRepositoryMock;

    /**
     * App\Repository\DBCompany $companyRepositoryMock.
     */
    private $companyRepositoryMock;

    /**
     * Lcobucci\JWT\Parser $jwtParser.
     */
    private $jwtParser;

    /**
     * Lcobucci\JWT\ValidationData $jwtValidation.
     */
    private $jwtValidation;

    /**
     * Lcobucci\JWT\Signer\Hmac\Sha256 $jwtSigner.
     */
    private $jwtSigner;

    /**
     * Lcobucci\JWT\Builder $jwtBuilder.
     */
    private $jwtBuilder;

    /**
     * Slim\Http\Request $requestMock.
     */
    private $requestMock;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Credential');
        $entityFactory->create('User');
        $entityFactory->create('Company');

        $this->credentialRepositoryMock = $this
            ->getMockBuilder(DBCredential::class)
            ->setMethods(['findByPubKey'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRepositoryMock = $this
            ->getMockBuilder(DBUser::class)
            ->setMethods(['findOrCreate'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyRepositoryMock = $this
            ->getMockBuilder(DBCompany::class)
            ->setMethods(['findById'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->disableOriginalConstructor()
            ->getMock();

        $this->jwtParser     = new JWT\Parser();
        $this->jwtValidation = new JWT\ValidationData();
        $this->jwtSigner     = new JWT\Signer\Hmac\Sha256();

        $this->jwtBuilder = new JWT\Builder();

        $this->requestMock = new class() extends Request {
            protected $attributes;

            public function __construct() {
                $this->attributes = [];
            }

            public function getAttributes() {
                return $this->attributes;
            }

            public function withAttribute($name, $value) {
                $this->attributes[$name] = $value;

                return $this;
            }
        };
    }

    /**
     * Gets the auth middleware with the provided $authRequest.
     *
     * @param int $authRequest The auth request
     *
     * @return AuthMiddleware The auth middleware.
     */
    private function getAuthMiddleware(int $authRequest) : AuthMiddleware {
        return new AuthMiddleware($this->credentialRepositoryMock, $this->userRepositoryMock, $this->companyRepositoryMock, $this->jwtParser, $this->jwtValidation, $this->jwtSigner, $authRequest);
    }

    private function getCredentialEntity() {
        return new CredentialEntity([
            ],
            $this->optimus
        );
    }

    private function generateToken(string $privateKey, array $claims) {
        foreach($claims as $key => $value) {
            $this->jwtBuilder->set($key, $value);
        }

        return $this->jwtBuilder
            ->sign($this->jwtSigner, $privateKey)
            ->getToken();
    }

    public function testHandleUserTokenSuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        $targetCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-user',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'companyId'  => $targetCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $actingUser = new UserEntity([
            'id'         => 1,
            'username'   => $targetCompany->username,
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->userRepositoryMock
            ->method('findOrCreate')
            ->willReturn($actingUser);

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($targetCompany);

        $claims = [
            'iss' => $credential->public,
            'sub' => $actingUser->username
        ];

        $token = $this->generateToken($credential->private, $claims);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingUser, $attributes['actingUser']);
        $this->assertSame($targetCompany, $attributes['targetCompany']);
        $this->assertSame($credential, $attributes['credential']);
    }

    public function testHandleUserTokenErrorInvalidToken() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, 'jwt.invalid.token']);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }

    }

    public function testHandleUserTokenErrorCredentialNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->throwException(new NotFound()));

        $claims = [
            'iss' => 'test',
            'sub' => 'test'
        ];

        $token = $this->generateToken('test', $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleUserTokenErrorSignatureVerificationFailed() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        $targetCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-user',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'companyId'  => $targetCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $actingUser = new UserEntity([
            'id'         => 1,
            'username'   => $targetCompany->username,
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->userRepositoryMock
            ->method('findOrCreate')
            ->willReturn($actingUser);

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($targetCompany);

        $claims = [
            'iss' => $credential->public,
            'sub' => $actingUser->username
        ];

        $token = $this->generateToken('wrong-sign-key', $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleUserTokenErrorMissingSubClaim() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        $targetCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-user',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'companyId'  => $targetCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($targetCompany);

        $claims = [
            'iss' => $credential->public
        ];

        $token = $this->generateToken($credential->private, $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleUserTokenErrorActingUserNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        $targetCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-user',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'companyId'  => $targetCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $actingUser = new UserEntity([
            'id'         => 1,
            'username'   => $targetCompany->username,
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->userRepositoryMock
            ->method('findOrCreate')
            ->will($this->throwException(new NotFound()));

        $claims = [
            'iss' => $credential->public,
            'sub' => $actingUser->username
        ];

        $token = $this->generateToken($credential->private, $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

            return $this->fail('Expecting NotFound exception');
        } catch (NotFound $e) {

        }
    }

    public function testHandleUserTokenErrorTargetCompanyNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_TOKEN);

        $targetCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-user',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'companyId'  => $targetCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $actingUser = new UserEntity([
            'id'         => 1,
            'username'   => $targetCompany->username,
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->userRepositoryMock
            ->method('findOrCreate')
            ->willReturn($actingUser);

        $this->companyRepositoryMock
            ->method('findById')
            ->will($this->throwException(new NotFound()));

        $claims = [
            'iss' => $credential->public,
            'sub' => $actingUser->username
        ];

        $token = $this->generateToken($credential->private, $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserToken', [$this->requestMock, $token]);

            return $this->fail('Expecting NotFound exception');
        } catch (NotFound $e) {

        }
    }

    public function testHandleUserPubKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PUBKEY);

        $credential = new CredentialEntity([
            'id'         => 1,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $targetUser = new UserEntity([
            'id'           => 1,
            'credentialId' => $credential->id,
            'username'     => 'username-test',
            'created_at'   => time(),
            'updated_at'   => time()],
            $this->optimus
        );

        $this->userRepositoryMock
            ->method('findByPubKey')
            ->willReturn($targetUser);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPubKey', [$this->requestMock, $credential->public]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($targetUser, $attributes['targetUser']);
    }

    public function testHandleUserPubKeyTargetUserNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PUBKEY);

        $credential = new CredentialEntity([
            'id'         => 1,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $targetUser = new UserEntity([
            'id'           => 1,
            'credentialId' => $credential->id,
            'username'     => 'username-test',
            'created_at'   => time(),
            'updated_at'   => time()],
            $this->optimus
        );

        $this->userRepositoryMock
            ->method('findByPubKey')
            ->will($this->throwException(new NotFound()));

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPubKey', [$this->requestMock, $credential->public]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleUserPrivKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PRIVKEY);

        $credential = new CredentialEntity([
            'id'         => 1,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $actingUser = new UserEntity([
            'id'           => 1,
            'credentialId' => $credential->id,
            'username'     => 'username-test',
            'created_at'   => time(),
            'updated_at'   => time()],
            $this->optimus
        );

        $this->userRepositoryMock
            ->method('findByPrivKey')
            ->willReturn($actingUser);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPrivKey', [$this->requestMock, $credential->private]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingUser, $attributes['actingUser']);
    }

    public function testHandleUserPrivKeyErrorTargetUserNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::USER_PRIVKEY);

        $credential = new CredentialEntity([
            'id'         => 1,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $actingUser = new UserEntity([
            'id'           => 1,
            'credentialId' => $credential->id,
            'username'     => 'username-test',
            'created_at'   => time(),
            'updated_at'   => time()],
            $this->optimus
        );

        $this->userRepositoryMock
            ->method('findByPrivKey')
            ->will($this->throwException(new NotFound()));

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPrivKey', [$this->requestMock, $credential->private]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleCompanyPubKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('public'),
            'private_key' => md5('private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPubKey', [$this->requestMock, $actingCompany->public_key]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingCompany, $attributes['actingCompany']);
    }

    public function testHandleCompanyPubKeyErrorActingCompanyNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('public'),
            'private_key' => md5('private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->will($this->throwException(new NotFound()));

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPubKey', [$this->requestMock, $actingCompany->public_key]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleCompanyPrivKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PRIVKEY);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('public'),
            'private_key' => md5('private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPrivKey', [$this->requestMock, $actingCompany->private_key]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingCompany, $attributes['actingCompany']);
    }

    public function testHandleCompanyPrivKeyErrorActingCompanyNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PRIVKEY);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('public'),
            'private_key' => md5('private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->will($this->throwException(new NotFound()));

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPrivKey', [$this->requestMock, $actingCompany->private_key]);

            return $this->fail('Expecting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleCredentialTokenSuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_TOKEN);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('acting-public'),
            'private_key' => md5('acting-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $targetCompany = new CompanyEntity([
            'id'          => 2,
            'username'    => 'target-company',
            'public_key'  => md5('target-public'),
            'private_key' => md5('target-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $issuerCredential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Issuer Credential Test',
            'slug'       => 'issuer-credential-test',
            'public'     => md5('issuer-public'),
            'private'    => md5('issuer-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $subjectCredential = new CredentialEntity([
            'id'         => 2,
            'company_id' => $targetCompany->id,
            'name'       => 'Subject Credential Test',
            'slug'       => 'subject-credential-test',
            'public'     => md5('subject-public'),
            'private'    => md5('subject-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->onConsecutiveCalls($issuerCredential, $subjectCredential));

        $this->companyRepositoryMock
            ->method('findById')
            ->will($this->onConsecutiveCalls($actingCompany, $targetCompany));

        $claims = [
            'iss' => $issuerCredential->public,
            'sub' => $subjectCredential->public
        ];

        $token = $this->generateToken($issuerCredential->private, $claims);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialToken', [$this->requestMock, $token]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingCompany, $attributes['actingCompany']);
        $this->assertSame($targetCompany, $attributes['targetCompany']);
        $this->assertSame($subjectCredential, $attributes['credential']);
    }

    public function testHandleCredentialTokenErrorInvalidToken() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_TOKEN);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialToken', [$this->requestMock, 'invalid.token']);

            return $this->fail('Expecting AppException');
        } catch(AppException $e) {

        }
    }

    public function testHandleCredentialTokenErrorIssuerCredentialNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_TOKEN);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('acting-public'),
            'private_key' => md5('acting-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $targetCompany = new CompanyEntity([
            'id'          => 2,
            'username'    => 'target-company',
            'public_key'  => md5('target-public'),
            'private_key' => md5('target-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $issuerCredential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Issuer Credential Test',
            'slug'       => 'issuer-credential-test',
            'public'     => md5('issuer-public'),
            'private'    => md5('issuer-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $subjectCredential = new CredentialEntity([
            'id'         => 2,
            'company_id' => $targetCompany->id,
            'name'       => 'Subject Credential Test',
            'slug'       => 'subject-credential-test',
            'public'     => md5('subject-public'),
            'private'    => md5('subject-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->throwException(new NotFound()));

        $claims = [
            'iss' => $issuerCredential->public,
            'sub' => $subjectCredential->public
        ];

        $token = $this->generateToken($issuerCredential->private, $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialToken', [$this->requestMock, $token]);

            return $this->fail('Expecting AppException');
        } catch(AppException $e) {

        }
    }

    public function testHandleCredentialTokenErrorMissingSub() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_TOKEN);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('acting-public'),
            'private_key' => md5('acting-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $targetCompany = new CompanyEntity([
            'id'          => 2,
            'username'    => 'target-company',
            'public_key'  => md5('target-public'),
            'private_key' => md5('target-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $issuerCredential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Issuer Credential Test',
            'slug'       => 'issuer-credential-test',
            'public'     => md5('issuer-public'),
            'private'    => md5('issuer-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $subjectCredential = new CredentialEntity([
            'id'         => 2,
            'company_id' => $targetCompany->id,
            'name'       => 'Subject Credential Test',
            'slug'       => 'subject-credential-test',
            'public'     => md5('subject-public'),
            'private'    => md5('subject-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->onConsecutiveCalls($issuerCredential, $subjectCredential));

        $this->companyRepositoryMock
            ->method('findById')
            ->will($this->onConsecutiveCalls($actingCompany, $targetCompany));

        $claims = [
            'iss' => $issuerCredential->public
        ];

        $token = $this->generateToken($issuerCredential->private, $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialToken', [$this->requestMock, $token]);

            return $this->fail('Expecting AppException');
        } catch(AppException $e) {

        }
    }

    public function testHandleCredentialTokenErrorActingCompanyNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_TOKEN);

        $actingCompany = new CompanyEntity([
            'id'          => 1,
            'username'    => 'acting-company',
            'public_key'  => md5('acting-public'),
            'private_key' => md5('acting-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $targetCompany = new CompanyEntity([
            'id'          => 2,
            'username'    => 'target-company',
            'public_key'  => md5('target-public'),
            'private_key' => md5('target-private'),
            'created_at'  => time(),
            'updated_at'  => time()],
            $this->optimus
        );

        $issuerCredential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Issuer Credential Test',
            'slug'       => 'issuer-credential-test',
            'public'     => md5('issuer-public'),
            'private'    => md5('issuer-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $subjectCredential = new CredentialEntity([
            'id'         => 2,
            'company_id' => $targetCompany->id,
            'name'       => 'Subject Credential Test',
            'slug'       => 'subject-credential-test',
            'public'     => md5('subject-public'),
            'private'    => md5('subject-private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->onConsecutiveCalls($issuerCredential, $subjectCredential));

        $this->companyRepositoryMock
            ->method('findById')
            ->will($this->throwException(new NotFound()));

        $claims = [
            'iss' => $issuerCredential->public,
            'sub' => $subjectCredential->public
        ];

        $token = $this->generateToken($issuerCredential->private, $claims);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialToken', [$this->requestMock, $token]);

            return $this->fail('Expecting AppException');
        } catch(AppException $e) {

        }
    }

    public function testHandleCredentialPubKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'       => 'Company Test',
            'slug'       => 'company-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->public]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingCompany, $attributes['actingCompany']);
        $this->assertSame($credential, $attributes['credential']);
    }

    public function testHandleCredentialPubKeyErrorCredentialNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'       => 'Company Test',
            'slug'       => 'company-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->will($this->throwException(new NotFound()));

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($actingCompany);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->public]);

            $this->fail('Excepting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleCredentialPubKeyErrorActingCompanyNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'       => 'Company Test',
            'slug'       => 'company-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPubKey')
            ->willReturn($credential);

        $this->companyRepositoryMock
            ->method('findById')
            ->will($this->throwException(new NotFound()));

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->public]);

            $this->fail('Excepting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleCredentialPrivKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PRIVKEY);

        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'       => 'Company Test',
            'slug'       => 'company-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPrivKey')
            ->willReturn($credential);

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPubKey', [$this->requestMock, $credential->private]);

        $attributes = $this->requestMock->getAttributes();

        $this->assertSame($actingCompany, $attributes['actingCompany']);
        $this->assertSame($credential, $attributes['credential']);
    }

    public function testHandleCredentialPrivKeyErrorCredentialNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'       => 'Company Test',
            'slug'       => 'company-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPrivKey')
            ->will($this->throwException(new NotFound()));

        $this->companyRepositoryMock
            ->method('findById')
            ->willReturn($actingCompany);

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPrivKey', [$this->requestMock, $credential->public]);

            $this->fail('Excepting AppException');
        } catch (AppException $e) {

        }
    }

    public function testHandleCredentialPrivKeyErrorActingCompanyNotFound() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);

        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'       => 'Company Test',
            'slug'       => 'company-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $credential = new CredentialEntity([
            'id'         => 1,
            'company_id' => $actingCompany->id,
            'name'       => 'Credential Test',
            'slug'       => 'credential-test',
            'public'     => md5('public'),
            'private'    => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->credentialRepositoryMock
            ->method('findByPrivKey')
            ->willReturn($credential);

        $this->companyRepositoryMock
            ->method('findById')
            ->will($this->throwException(new NotFound()));

        try {
            $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCredentialPrivKey', [$this->requestMock, $credential->public]);

            $this->fail('Excepting AppException');
        } catch (AppException $e) {

        }
    }

}
