<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Middleware;

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
 * Authorization Middleware
 *
 * Extracts authorization from request and stores Acting/Target
 * Subjects (User and/or Company) to request.
 */
class Auth {
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
     * @const None No authorization
     */
    const None = 0x00;
    /**
     * Company performing public and private actions on a User's behalf
     * Scope: Integration.
     *
     * @const UserToken User Token
     */
    const UserToken = 0x01;
    /**
     * User performing public actions
     * Scope: System.
     *
     * @const UserPubKey User Public Key
     */
    const UserPubKey = 0x02;
    /**
     * User performing User Management actions
     * Scope: System.
     *
     * @const UserPrivKey User Private Key
     */
    const UserPrivKey = 0x04;
    /**
     * Company performing public actions
     * Scope: System.
     *
     * @const CompanyPubKey Company Public Key
     */
    const CompanyPubKey = 0x08;
    /**
     * Company performing Company Management actions
     * Scope: System.
     *
     * @const CompanyPrivKey Company Private Key
     */
    const CompanyPrivKey = 0x10;
    /**
     * Credential performing public and private actions on a Credential's behalf
     * Scope: Integration.
     *
     * @const CredentialToken Credential Token
     */
    const CredentialToken = 0x20;
    /**
     * Credential performing public actions
     * Scope: Integration.
     *
     * @const CredentialPubKey Credential Public Key
     */
    const CredentialPubKey = 0x30;
    /**
     * Credential performing private actions
     * Scope: Integration.
     *
     * @const CredentialPrivKey Credential Private Key
     */
    const CredentialPrivKey = 0x40;

    /**
     * Returns an authorization setup array based on available
     * authorization constants and internal handler functions.
     *
     * @return array
     */
    private function authorizationSetup() {
        return [
            self::UserToken => [
                'name'    => 'UserToken',
                'label'   => 'User Token',
                'handler' => 'handleUserToken'
            ],
            self::UserPubKey => [
                'name'    => 'UserPubKey',
                'label'   => 'User Public Key',
                'handler' => 'handleUserPubKey'
            ],
            self::UserPrivKey => [
                'name'    => 'UserPrivKey',
                'label'   => 'User Private Key',
                'handler' => 'handleUserPrivKey'
            ],
            self::CompanyPubKey => [
                'name'    => 'CompanyPubKey',
                'label'   => 'Company Public Key',
                'handler' => 'handleCompanyPubKey'
            ],
            self::CompanyPrivKey => [
                'name'    => 'CompanyPrivKey',
                'label'   => 'Company Private Key',
                'handler' => 'handleCompanyPrivKey'
            ],
            self::CredentialToken => [
                'name'    => 'CredentialToken',
                'label'   => 'Credential Token',
                'handler' => 'handleCredentialToken'
            ],
            self::CredentialPubKey => [
                'name'    => 'CredentialPubKey',
                'label'   => 'Credential Public Key',
                'handler' => 'handleCredentialPubKey'
            ],
            self::CredentialPrivKey => [
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
    private function extractAuthorization(ServerRequestInterface $request, $name) {
        $name  = ucfirst($name);
        $regex = sprintf('/^%s ([a-zA-Z0-9]+)$/', $name);
        if (preg_match($regex, $request->getHeaderLine('Authorization'), $matches))
            return $matches[1];

        $name        = lcfirst($name);
        $queryParams = $request->getQueryParams();
        if (isset($queryParams[$name]))
            return $queryParams[$name];

        return null;
    }

    /**
     * Handles request Authorization based on User Token.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqToken
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleUserToken(ServerRequestInterface $request, $reqToken) {
        $token = $this->jwtParser->parse($reqToken);

        // Ensures JWT Audience is the current API
        $this->jwtValidation->setAudience(sprintf('https://api.veridu.com/%s', __VERSION__));
        if (! $token->validate($this->jwtvalidation))
            throw new \Exception('Token Validation Failed');

        // Retrieves JWT Issuer
        $pubKey     = $token->getClaim('iss');
        $credential = $this->credentialRepository->findByPubKey($pubKey);
        if ($credential->isEmpty())
            throw new \Exception('Invalid Credential');

        // JWT Signature Verification
        if (! $token->verify($this->jwtSigner, $credential->private_key))
            throw new \Exception('Token Verification Failed');

        // Retrieves JWT Subject
        if (! $token->hasClaim('sub'))
            throw new \Exception('Missing Subject Claim');
        $userName = $token->getClaim('sub');

        // If it's a new user, creates it
        $actingUser = $this->userRepository->findOrCreate($userName, $credential->id);

        // Retrieves Credential's owner
        $targetCompany = $this->companyRepository->findById($credential->company_id);

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
    private function handleUserPubKey(ServerRequestInterface $request, $reqKey) {
        $targetUser = $this->userRepository->findByPubKey($reqKey);
        if ($targetUser->isEmpty())
            throw new \Exception('Invalid Credential');

        return $request
            // Stores Target User for future use
            ->withAttribute('targetUser', $targetUser);
    }

    /**
     * Handles request Authorization based on User Private Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleUserPrivKey(ServerRequestInterface $request, $reqKey) {
        $actingUser = $this->userRepository->findByPrivKey($reqKey);
        if ($actingUser->isEmpty())
            throw new \Exception('Invalid Credential');

        return $request
            // Stores Acting User for future use
            ->withAttribute('actingUser', $actingUser);
    }

    /**
     * Handles request Authorization based on Company Public Key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string                                   $reqKey
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function handleCompanyPubKey(ServerRequestInterface $request, $reqKey) {
        $actingCompany = $this->companyRepository->findByPubKey($reqKey);
        if ($actingCompany->isEmpty())
            throw new \Exception('Invalid Credential');

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
    private function handleCompanyPrivKey(ServerRequestInterface $request, $reqKey) {
        try {
            $actingCompany = $this->companyRepository->findByPrivKey($reqKey);

            return $request
                // Stores Acting Company for future use
                ->withAttribute('actingCompany', $actingCompany);
        } catch (NotFound $exception) {
            throw new \Exception('Invalid Credential');
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
    private function handleCredentialToken(ServerRequestInterface $request, $reqToken) {
        $token = $this->jwtParser->parse($reqToken);

        // Ensures JWT Audience is the current API
        $this->jwtValidation->setAudience(sprintf('https://api.veridu.com/%s', __VERSION__));
        if (! $token->validate($this->jwtvalidation))
            throw new \Exception('Token Validation Failed');

        // Retrieves JWT Issuer
        $issuerKey        = $token->getClaim('iss');
        $issuerCredential = $this->credentialRepository->findByPubKey($issuerKey);

        if ($issuerCredential->isEmpty())
            throw new \Exception('Invalid Issuer Credential');

        // JWT Signature Verification
        if (! $token->verify($this->jwtSigner, $issuerCredential->private_key))
            throw new \Exception('Token Verification Failed');

        // Retrieves JWT Subject
        if (! $token->hasClaim('sub'))
            throw new \Exception('Missing Subject Claim');
        $subjectKey        = $token->getClaim('sub');
        $subjectCredential = $this->credentialRepository->findByPubKey($subjectKey);

        if ($subjectCredential->isEmpty())
            throw new \Exception('Invalid Subject Credential');

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
    private function handleCredentialPubKey(ServerRequestInterface $request, $reqKey) {
        $credential = $this->credentialRepository->findByPubKey($reqKey);
        if ($credential->isEmpty())
            throw new \Exception('Invalid Credential');

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
    private function handleCredentialPrivKey(ServerRequestInterface $request, $reqKey) {
        $credential = $this->credentialRepository->findByPrivKey($reqKey);
        if ($credential->isEmpty())
            throw new \Exception('Invalid Credential');

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
        $authorizationRequirement = self::None
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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {

        $hasAuthorization   = ($this->authorizationRequirement == self::None);
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
                    $request = $this->$authorizationInfo['handler']($request, $authorization);
                    // Authorization has been found and validated
                    $hasAuthorization = true;
                }
            }
        }

        // Request has proper Authorization, proceed with regular process
        if ($hasAuthorization) {
            $routeInfo = $request->getAttribute('routeInfo');

            // Resolves {userName} route argument
            if (! empty($routeInfo[2]['userName'])) {
                // Loads Target User
                if ($routeInfo[2]['userName'] === '_self') {
                    // Self Reference for User Token / User Private Key
                    $user = $request->getAttribute('actingUser');
                    if (empty($user))
                        throw new \Exception('InvalidUserNameReference');
                } else {
                    // Load User
                    $company = $request->getAttribute('targetCompany');
                    if (empty($company))
                        $company = $request->getAttribute('actingCompany');
                    if (empty($company))
                        throw new \Exception('InvalidRequest');
                    $user = $this->userRepository->findOrCreate($routeInfo[2]['userName'], $company->id);
                }

                // Stores Target User for future use
                $request = $request->withAttribute('targetUser', $user);
            }

            // Resolves {companySlug} route argument
            if (! empty($routeInfo[2]['companySlug'])) {
                // Loads Target Company
                if ($routeInfo[2]['companySlug'] === '_self') {
                    // Self Reference for Credential Token / Compamny Private Key
                    $targetCompany = $request->getAttribute('actingCompany');
                    if (empty($targetCompany))
                        throw new \Exception('InvalidCompanyNameReference');
                } else {
                    // Load Company
                    $targetCompany = $this->companyRepository->findBySlug($routeInfo[2]['companySlug']);
                    if (empty($targetCompany))
                        throw new \Exception('InvalidCompanyNameReference');
                    // Checks if access hierarchy is respected (Parent to Child or Company to itself)
                    if ($this->authorizationRequirement != self::None) {
                        $actingCompany = $request->getAttribute('actingCompany');
                        if (($actingCompany->id != $targetCompany->id) && ($actingCompany->id != $targetCompany->parent_id))
                            throw new \Exception('AccessDenied');
                    }
                }

                // Stores Target Company for future use
                $request = $request->withAttribute('targetCompany', $targetCompany);
            }

            return $next($request, $response);
        }

        throw new \Exception('AuthorizationMissing - Authorization details missing. Valid Authorization: ' . implode(', ', $validAuthorization), 403);
    }
}
