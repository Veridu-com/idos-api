<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use App\Entity\Company as CompanyEntity;
use App\Entity\Credential as CredentialEntity;
use App\Entity\User as UserEntity;
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
            'id'         => 1,
            'credentialId' => $credential->id,
            'username'   => 'username-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->userRepositoryMock
            ->method('findByPubKey')
            ->willReturn($targetUser);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPubKey', [$this->requestMock, $credential->public]);

        $attributes = $this->requestMock->getAttributes();
        
        $this->assertSame($targetUser, $attributes['targetUser']);
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
            'id'         => 1,
            'credentialId' => $credential->id,
            'username'   => 'username-test',
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->userRepositoryMock
            ->method('findByPrivKey')
            ->willReturn($actingUser);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleUserPrivKey', [$this->requestMock, $credential->private]);

        $attributes = $this->requestMock->getAttributes();
        
        $this->assertSame($actingUser, $attributes['actingUser']);
    }

    public function testHandleCompanyPubKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PUBKEY);
     
        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-company',
            'public_key' => md5('public'),
            'private_key' => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPubKey', [$this->requestMock, $actingCompany->public_key]);

        $attributes = $this->requestMock->getAttributes();
        
        $this->assertSame($actingCompany, $attributes['actingCompany']);
    }

    public function testHandleCompanyPrivKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::COMP_PRIVKEY);
     
        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-company',
            'public_key' => md5('public'),
            'private_key' => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPrivKey', [$this->requestMock, $actingCompany->private_key]);

        $attributes = $this->requestMock->getAttributes();
        
        $this->assertSame($actingCompany, $attributes['actingCompany']);
    }

    public function testHandleCredentialTokenSuccess() {
        /*$authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_TOKEN);
     
        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'username'   => 'acting-company',
            'public_key' => md5('public'),
            'private_key' => md5('private'),
            'created_at' => time(),
            'updated_at' => time()],
            $this->optimus
        );

        $this->companyRepositoryMock
            ->method('findByPubKey')
            ->willReturn($actingCompany);

        $this->requestMock = $this->invokePrivateMethod($authMiddleware, 'handleCompanyPrivKey', [$this->requestMock, $actingCompany->private_key]);

        $attributes = $this->requestMock->getAttributes();
        
        $this->assertSame($actingCompany, $attributes['actingCompany']);*/
    }

    public function testHandleCredentialPubKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PUBKEY);
     
        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'   => 'Company Test',
            'slug'	=> 'company-test',
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

    public function testHandleCredentialPrivKeySuccess() {
        $authMiddleware = $this->getAuthMiddleware(AuthMiddleware::CRED_PRIVKEY);
     
        $actingCompany = new CompanyEntity([
            'id'         => 1,
            'name'   => 'Company Test',
            'slug'	=> 'company-test',
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

}
