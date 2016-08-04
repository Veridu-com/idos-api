<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\AppException;
use App\Exception\NotFound;
use App\Repository\CompanyInterface;
use App\Repository\CredentialInterface;
use App\Repository\UserInterface;
use Lcobucci\JWT\Parser as JWTParser;
use Lcobucci\JWT\Signer\Hmac\Sha256 as JWTSigner;
use Lcobucci\JWT\ValidationData as JWTValidation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Authorization Middleware.
 *
 * Scope: Application.
 * Extracts authorization from request and stores Acting/Target
 * Subjects (User and/or Company) to request.
 */
class Auth implements MiddlewareInterface {
    /**
     * Credential Repository.
     *
     * @var App\Repository\CredentialInterface
     */
    private $credentialRepository;
    /**
     * User Repository.
     *
     * @var App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Company Repository.
     *
     * @var App\Repository\Companyinterface
     */
    private $companyRepository;
    /**
     * JWT Parser.
     *
     * @var \Lcobucci\JWT\Parser
     */
    private $jwtParser;
    /**
     * JWT Validation Data.
     *
     * @var \Lcobucci\JWT\ValidationData
     */
    private $jwtValidation;
    /**
     * JWT SHA256-HMAC Signer.
     *
     * @var \Lcobucci\JWT\Signer\Hmac\Sha256
     */
    private $jwtSigner;
    /**
     * Authorization Requirement Bitmask.
     *
     * @var int
     */
    private $authorizationRequirement;

    /**
     * Public access
     * Scope: Public.
     *
     * @const NONE No authorization
     */
    const NONE = 0x00;
    /**
     * Company performing public and private actions on a User's behalf
     * Scope: Integration.
     *
     * @const USER_TOKEN User Token
     */
    const USER_TOKEN = 0x01;
    /**
     * User performing public actions
     * Scope: System.
     *
     * @const USER_PUBKEY User Public Key
     */
    const USER_PUBKEY = 0x02;
    /**
     * User performing User Management actions
     * Scope: System.
     *
     * @const USER_PRIVKEY User Private Key
     */
    const USER_PRIVKEY = 0x04;
    /**
     * Company performing public actions
     * Scope: System.
     *
     * @const COMP_PUBKEY Company Public Key
     */
    const COMP_PUBKEY = 0x08;
    /**
     * Company performing Company Management actions
     * Scope: System.
     *
     * @const COMP_PRIVKEY Company Private Key
     */
    const COMP_PRIVKEY = 0x10;
    /**
     * Credential performing public and private actions on a Credential's behalf
     * Scope: Integration.
     *
     * @const CRED_TOKEN Credential Token
     */
    const CRED_TOKEN = 0x20;
    /**
     * Credential performing public actions
     * Scope: Integration.
     *
     * @const CRED_PUBKEY Credential Public Key
     */
    const CRED_PUBKEY = 0x30;
    /**
     * Credential performing private actions
     * Scope: Integration.
     *
     * @const CRED_PRIVKEY Credential Private Key
     */
    const CRED_PRIVKEY = 0x40;

    /**
     * Returns an authorization setup array based on available
     * authorization constants and internal handler functions.
     *
     * @return array
     */
    private function authorizationSetup() : array {
        return [
            self::USER_TOKEN => [
                'name'    => 'UserToken',
                'label'   => 'User Token',
                'handler' => 'handleUserToken'
            ],
            self::USER_PUBKEY => [
                'name'    => 'UserPubKey',
                'label'   => 'User Public Key',
                'handler' => 'handleUserPubKey'
            ],
            self::USER_PRIVKEY => [
                'name'    => 'UserPrivKey',
                'label'   => 'User Private Key',
                'handler' => 'handleUserPrivKey'
            ],
            self::COMP_PUBKEY => [
                'name'    => 'CompanyPubKey',
                'label'   => 'Company Public Key',
                'handler' => 'handleCompanyPubKey'
            ],
            self::COMP_PRIVKEY => [
                'name'    => 'CompanyPrivKey',
                'label'   => 'Company Private Key',
                'handler' => 'handleCompanyPrivKey'
            ],
            self::CRED_TOKEN => [
                'name'    => 'CredentialToken',
                'label'   => 'Credential Token',
                'handler' => 'handleCredentialToken'
            ],
            self::CRED_PUBKEY => [
                'name'    => 'CredentialPubKey',
                'label'   => 'Credential Public Key',
                'handler' => 'handleCredentialPubKey'
            ],
            self::CRED_PRIVKEY => [
                'name'    => 'CredentialPrivKey',
                'label'   => 'Credential Private Key',
                'handler' => 'handleCredentialPrivKey'
            ]
        ];
    }

    /**
     * Extracts Authorization from Request Header or Request Query Parameter.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $name
     *
     * @return string|null
     */
    private function extractAuthorization(ServerRequestInterface $request, string $name) {
        $name  = ucfirst($name);
        $regex = sprintf('/^%s ([a-zA-Z0-9]+)$/', $name);
        if (preg_match($regex, $request->getHeaderLine('Authorization'), $matches))
            return $matches[1];

        $name        = lcfirst($name);
        $queryParams = $request->getQueryParams();
        if (isset($queryParams[$name]))
            return $queryParams[$name];

        return;
    }

    /**
     * Handles request Authorization based on User Token.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqToken
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleUserToken(ServerRequestInterface $request, string $reqToken) : ServerRequestInterface {
        $token = $this->jwtParser->parse($reqToken);

        // Ensures JWT Audience is the current API
        $this->jwtValidation->setAudience(sprintf('https://api.veridu.com/%s', __VERSION__));
        if (! $token->validate($this->jwtvalidation)) {
            throw new AppException('Token Validation Failed');
        }

        // Retrieves JWT Issuer
        $pubKey     = $token->getClaim('iss');
        $credential = $this->credentialRepository->findByPubKey($pubKey);
        if ($credential->isEmpty()) {
            throw new AppException('Invalid Credential');
        }

        // JWT Signature Verification
        if (! $token->verify($this->jwtSigner, $credential->private_key))
            throw new AppException('Token Verification Failed');

        // Retrieves JWT Subject
        if (! $token->hasClaim('sub')) {
            throw new AppException('Missing Subject Claim');
        }
        $userName = $token->getClaim('sub');

        // If it's a new user, creates it
        $actingUser = $this->userRepository->findOrCreate($userName, $credential->id);

        // Retrieves Credential's owner
        $targetCompany = $this->companyRepository->findById($credential->companyId);

        return $request
            // Stores Acting User for future use
            ->withAttribute('actingUser', $actingUser)

            // Stores Target Company for future use
            ->withAttribute('targetCompany', $targetCompany)

            // Stores Credential for future use
            ->withAttribute('credential', $credential);
    }

    /**
     * Handles request Authorization based on User Public Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleUserPubKey(ServerRequestInterface $request, string $reqKey) : ServerRequestInterface {
        try {
            $targetUser = $this->userRepository->findByPubKey($reqKey);
        } catch (NotFound $e) {
            throw new AppException('Invalid Credential');
        }

        // Stores Target User for future use
        return $request->withAttribute('targetUser', $targetUser);
    }

    /**
     * Handles request Authorization based on User Private Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleUserPrivKey(ServerRequestInterface $request, string $reqKey) : ServerRequestInterface {
        try {
            $actingUser = $this->userRepository->findByPrivKey($reqKey);
        } catch (NotFound $e) {
            throw new AppException('Invalid Credential');
        }

        // Stores Acting User for future use
        return $request->withAttribute('actingUser', $actingUser);
    }

    /**
     * Handles request Authorization based on Company Public Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleCompanyPubKey(ServerRequestInterface $request, string $reqKey) : ServerRequestInterface {
        $actingCompany = $this->companyRepository->findByPubKey($reqKey);
        if ($actingCompany->isEmpty())
            throw new AppException('Invalid Credential');

        return $request
            // Stores Acting Company for future use
            ->withAttribute('actingCompany', $actingCompany);
    }

    /**
     * Handles request Authorization based on Company Private Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleCompanyPrivKey(ServerRequestInterface $request, string $reqKey) : ServerRequestInterface {
        try {
            $actingCompany = $this->companyRepository->findByPrivKey($reqKey);

            return $request
                // Stores Acting Company for future use
                ->withAttribute('actingCompany', $actingCompany);
        } catch (NotFound $exception) {
            throw new AppException('Invalid Credential');
        }
    }

    /**
     * Handles request Authorization based on Credential Token.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqToken
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleCredentialToken(ServerRequestInterface $request, string $reqToken) : ServerRequestInterface {
        $token = $this->jwtParser->parse($reqToken);

        // Ensures JWT Audience is the current API
        $this->jwtValidation->setAudience(sprintf('https://api.veridu.com/%s', __VERSION__));
        if (! $token->validate($this->jwtvalidation))
            throw new AppException('Token Validation Failed');

        // Retrieves JWT Issuer
        $issuerKey        = $token->getClaim('iss');
        $issuerCredential = $this->credentialRepository->findByPubKey($issuerKey);

        if ($issuerCredential->isEmpty())
            throw new AppException('Invalid Issuer Credential');

        // JWT Signature Verification
        if (! $token->verify($this->jwtSigner, $issuerCredential->private_key))
            throw new AppException('Token Verification Failed');

        // Retrieves JWT Subject
        if (! $token->hasClaim('sub'))
            throw new AppException('Missing Subject Claim');
        $subjectKey        = $token->getClaim('sub');
        $subjectCredential = $this->credentialRepository->findByPubKey($subjectKey);

        if ($subjectCredential->isEmpty())
            throw new AppException('Invalid Subject Credential');

        // Retrieves Issuer Credential's owner
        $actingCompany = $this->companyRepository->findById($issuerCredential->company_id);

        // Retrieves Subject Credential's owner
        $targetCompany = $this->companyRepository->findById($subjectCredential->company_id);

        return $request
            // Stores Acting Company for future use
            ->withAttribute('actingCompany', $actingCompany)

            // Stores Target Company for future use
            ->withAttribute('targetCompany', $targetCompany)

            // Stores Credential for future use
            ->withAttribute('credential', $subjectCredential);
    }

    /**
     * Handles request Authorization based on Credential Public Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleCredentialPubKey(ServerRequestInterface $request, string $reqKey) : ServerRequestInterface {
        $credential = $this->credentialRepository->findByPubKey($reqKey);
        if ($credential->isEmpty())
            throw new AppException('Invalid Credential');

        // Retrieves Credential's owner
        $actingCompany = $this->companyRepository->findById($credential->company_id);

        return $request
            // Stores Acting Company for future use
            ->withAttribute('actingCompany', $actingCompany)

            // Stores Credential for future use
            ->withAttribute('credential', $credential);
    }

    /**
     * Handles request Authorization based on Credential Private Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleCredentialPrivKey(ServerRequestInterface $request, string $reqKey) : ServerRequestInterface {
        $credential = $this->credentialRepository->findByPrivKey($reqKey);
        if ($credential->isEmpty())
            throw new AppException('Invalid Credential');

        // Retrieves Credential's owner
        $actingCompany = $this->companyRepository->findById($credential->company_id);

        return $request
            // Stores Acting Company for future use
            ->withAttribute('actingCompany', $actingCompany)

            // Stores Credential for future use
            ->withAttribute('credential', $credential);
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\CredentialInterface $credentialRepository
     * @param App\Repository\UserInterface       $userRepository
     * @param App\Repository\CompanyInterface    $companyRepository
     * @param \Lcobucci\JWT\Parser               $jwtParser
     * @param \Lcobucci\JWT\ValidationData       $jwtValidation
     * @param \Lcobucci\JWT\Signer\Hmac\Sha256   $jwtSigner
     * @param int                                $authorizationRequirement
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $credentialRepository,
        UserInterface $userRepository,
        CompanyInterface $companyRepository,
        JWTParser $jwtParser,
        JWTValidation $jwtValidation,
        JWTSigner $jwtSigner,
        int $authorizationRequirement = self::NONE
    ) {
        $this->credentialRepository = $credentialRepository;
        $this->userRepository       = $userRepository;
        $this->companyRepository    = $companyRepository;

        $this->jwtParser     = $jwtParser;
        $this->jwtValidation = $jwtValidation;
        $this->jwtSigner     = $jwtSigner;

        $this->authorizationRequirement = $authorizationRequirement;
    }

    /**
     * Middleware execution, tries to extract authorization key from request and creates
     * request arguments for Acting User, Target User, Acting Company, Target Company and Credential.
     *
     * Acting User: the user that is performing the system action
     * Target User: the user that is receiving the system action
     * Acting Company: the company that is performing the system action
     * Target Company: the company that is receiving the system action
     * Credential: the credential used during the request (may be missing)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     *
     * @throws App\Exception\AppException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) : ResponseInterface {
        $hasAuthorization   = ($this->authorizationRequirement == self::NONE);
        $validAuthorization = [];

        // Authorization Handling Loop
        foreach ($this->authorizationSetup() as $level => $authorizationInfo) {
            if ($hasAuthorization)
                break;
            if (($this->authorizationRequirement & $level) == $level) {
                // Tries to extract Authorization from Request
                $authorization = $this->extractAuthorization($request, $authorizationInfo['name']);
                if (empty($authorization))
                    $validAuthorization[] = $authorizationInfo['label'];
                else {
                    // Handles Authorization validation and Request Argument creation
                    $request = $this->{$authorizationInfo['handler']}($request, $authorization);
                    // Authorization has been found and validated
                    $hasAuthorization = true;
                }
            }
        }

        // Request has proper Authorization, proceed with regular process
        if ($hasAuthorization) {
            $routeInfo      = $request->getAttribute('routeInfo');
            $companySlug    = empty($routeInfo[2]['companySlug']) ? null : $routeInfo[2]['companySlug'];
            $userName       = empty($routeInfo[2]['userName']) ? null : $routeInfo[2]['userName'];

            // Resolves {companySlug} route argument
            if ($companySlug) {
                $request = $this->populateRequestCompanies($companySlug, $request);
            }

            // Resolves {userName} route argument
            if ($userName) {
                $request = $this->populateRequestUsers($userName, $request);
            }

            return $next($request, $response);
        }

        throw new AppException('AuthorizationMissing - Authorization details missing. Valid Authorization: ' . implode(', ', $validAuthorization), 403);
    }

    /**
     * Populates the request with the found user on the request.
     *
     * @param string                                   $username The username
     * @param \Psr\Http\Message\ServerRequestInterface $request  The request object
     * 
     * @return \Psr\Http\Message\ServerRequestInterface $request   The modified request object
     */
    private function populateRequestUsers(string $username, ServerRequestInterface $request) : ServerRequestInterface {
            // Loads Target User
            if ($username === '_self') {
                // Self Reference for User Token / User Private Key
                $user = $request->getAttribute('actingUser');
                if (empty($user)){
                    throw new AppException('InvalidUserNameReference');
                }
            } else {
                // Load User
                $company = $request->getAttribute('targetCompany');
                if (empty($company)) {
                    $company = $request->getAttribute('actingCompany');
                }
                if (empty($company)) {
                    throw new AppException('InvalidRequest');
                }

                // @FIXME
                // how it will find username by user if NO credential was given? 
                // 1. username repeats inside company
                // 2. Can't find right credential since targetCompany may have N credentials
                $user = $this->userRepository->findOneByUsernameAndCredential($username, $request->getAttribute('credential'));
            }

            // Stores Target User for future use
            $request = $request->withAttribute('targetUser', $user);

            return $request;
    }

    /**
     * Populates the request with the found companies on the request.
     *
     * @param string                                   $username The username
     * @param \Psr\Http\Message\ServerRequestInterface $request  The request object
     * 
     * @return \Psr\Http\Message\ServerRequestInterface $request   The modified request object
     */
    private function populateRequestCompanies(string $companySlug, ServerRequestInterface $request) : ServerRequestInterface {
        // Loads Target Company
        if ($companySlug === '_self') {
            // Self Reference for Credential Token / Compamny Private Key
            $targetCompany = $request->getAttribute('actingCompany');
            if (empty($targetCompany))
                throw new AppException('InvalidCompanyNameReference');
        } else {
            // Load Company
            $targetCompany = $this->companyRepository->findBySlug($companySlug);

            if (empty($targetCompany))
                throw new AppException('InvalidCompanyNameReference');
            // Checks if access hierarchy is respected (Parent to Child or Company to itself)
            if ($this->authorizationRequirement != self::NONE) {
                $actingCompany = $request->getAttribute('actingCompany');

            }
        }

        // TODO: When there is a acting user there's no need for this test
        // if (($actingCompany->id != $targetCompany->id) && ($actingCompany->id != $targetCompany->parent_id))
        //     throw new AppException('AccessDenied');

        // Stores Target Company for future use
        return $request->withAttribute('targetCompany', $targetCompany);
    }
}
