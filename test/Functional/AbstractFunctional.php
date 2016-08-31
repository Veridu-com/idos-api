<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional;

use App\Helper\Token;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Slim\App;
use Slim\Http\Body;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 * AbstractFunctional Class.
 *
 * Join all common methods of the other functional classes.
 */
abstract class AbstractFunctional extends \PHPUnit_Framework_TestCase {
    /**
     * Slim's Application Instance.
     *
     * @var \Slim\App
     */
    private $app;

    /**
     * Message of the errors of a failed schema assertion.
     *
     * @var string
     */
    protected $schemaErrors;

    /**
     * Entities populated via populate() method.
     *
     * @see self::populate()
     *
     * @var array
     */
    protected $entities;

    /**
     * HTTP method of the test.
     *
     * @var string
     */
    protected $httpMethod;

    /**
     * URI property of the test.
     *
     * @var string
     */
    protected $uri;

    /**
     * Runs one time before any method of the Child classes.
     */
    public static function setUpBeforeClass() {
        $phinxTextWrapper = new TextWrapper(new PhinxApplication());
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'testing');
        $phinxTextWrapper->getRollback('testing', 0);
        $phinxTextWrapper->getMigrate();
        $phinxTextWrapper->getSeed();
    }

    /**
     * Load all the dependencies for the aplication.
     *
     * @return Slim\App $app
     */
    protected function getApp() : App {
        if ($this->app) {
            return $this->app;
        }

        $app = new App(
            ['settings' => $GLOBALS['appSettings']]
        );

        require_once __ROOT__ . '/../config/dependencies.php';
        require_once __ROOT__ . '/../config/middleware.php';
        require_once __ROOT__ . '/../config/handlers.php';
        require_once __ROOT__ . '/../config/routes.php';

        $this->app = $app;

        return $app;
    }

    /**
     * Process the request.
     *
     * @param Request $request
     *
     * @return ResponseInterface response
     */
    protected function process(Request $request) : Response {
        return $this->getApp()->process($request, new Response());
    }

    /**
     *  Populates the $entities property of the instance querying the given URI.
     *
     *  @param string $uri URI to be queried
     *  @param string $method URI to be queried
     *  @param array $params Environment parameters
     *  @param array $body Request body
     *
     *  @return void
     */
    protected function populate(string $uri, string $method = 'GET', array $params = []) {
        $environment = $this->createEnvironment(
            array_merge(
                [
                    'REQUEST_URI'    => $uri,
                    'REQUEST_METHOD' => $method
                ],
                $params
            )
        );

        $request  = $this->createRequest($environment);
        $response = $this->process($request);
        $body     = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 403) {
            $this->entities = [];
        } else {
            $this->entities = $body['data'];
        }
    }

    /**
     * Helper to get a random entity.
     *
     * @param int|bool $index
     *
     * @return array $this->entities
     */
    protected function getRandomEntity($index = false) : array {
        if (! $this->entities) {
            throw new \RuntimeException('Test instance not populated, call populate() method before calling getRandomEntity() method.');
        }

        if ($index === false) {
            $index = mt_rand(0, (count($this->entities) - 1));
        }

        return $this->entities[$index];
    }

    /**
     * Mocks the environment.
     *
     * @param array $options Environment options
     *
     * @return The mocked environment
     */
    protected function createEnvironment(array $options = []) : Environment {
        $defaults = [
            'REQUEST_URI'    => $this->uri,
            'REQUEST_METHOD' => $this->httpMethod,
            'SCRIPT_NAME'    => '/index.php',
            'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
        ];

        return Environment::mock(array_merge($defaults, $options));
    }

    /**
     * Creates the Request based on the mocked environment and the request body.
     *
     * @param Environment|null $environment
     * @param StreamInterface  $body        Request body
     *
     * @return RequestInterface $request
     */
    protected function createRequest(Environment $environment = null, $body = null) : Request {
        if ($environment === null) {
            $environment = $this->createEnvironment();
        }

        $requestBody = new RequestBody();

        if ($body) {
            $requestBody->write($body);
        }

        $request = new Request(
            $environment->get('REQUEST_METHOD'),
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            $requestBody
        );

        return $request;
    }

    /**
     * Validates the schemas given the schema file and the response body.
     *
     * @param string $schemaFile
     * @param  $bodyResponse
     *
     * @return bool $validator->isValid
     */
    protected function validateSchema(string $schemaFile, $bodyResponse) : bool {
        $schemaFile = ltrim($schemaFile, '/');
        $resolver   = new RefResolver(new UriRetriever(), new UriResolver());
        $schema     = $resolver->resolve(
            sprintf(
                'file://' . __DIR__ . '/../../schema/%s',
                $schemaFile
            )
        );
        $validator = new Validator();

        $validator->check(
            $bodyResponse,
            $schema
        );

        if (! $validator->isValid()) {
            $this->getSchemaErrors($validator);
        }

        return $validator->isValid();
    }

    /**
     * Gets the schema Errors if something went wrong in $this->validateSchema().
     *
     * @param Validator $validator
     */
    protected function getSchemaErrors(Validator $validator) {
        $this->schemaErrors = '';
        foreach ($validator->getErrors() as $error) {
            $this->schemaErrors .= sprintf("[%s] %s\n", $error['property'], $error['message']);
        }
    }

    /**
     * Generates a valid user token to be used on requests.
     *
     * @param string|null $userName          Overrides the default userName value (usr001)
     * @param string|null $credentialPubKey  Overrides the default credential
     * @param string|null $credentialPrivKey Overrides the default credential
     *
     * @return string
     */
    protected function userToken(
        $userName = 'usr001',
        $credentialPubKey = null,
        $credentialPrivKey = null
    ) {
        if ((empty($credentialPubKey)) || (empty($credentialPrivKey))) {
            $credentialPubKey  = '4c9184f37cff01bcdc32dc486ec36961';
            $credentialPrivKey = '2c17c6393771ee3048ae34d6b380c5ec';
        }

        return Token::generateUserToken(
            $userName,
            $credentialPubKey,
            $credentialPrivKey
        );
    }

    /**
     * Generates a valid user token header to be used on requests.
     *
     * @param string|null $token Overrides the default token
     *
     * @return string
     */
    protected function userTokenHeader($token = null) {
        if (empty($token)) {
            $token = $this->userToken();
        }

        return sprintf('UserToken %s', $token);
    }

    /**
     * Generates a valid company token to be used on requests.
     *
     * @param string|null $subject        Overrides the default subject value (4c9184f37cff01bcdc32dc486ec36961:usr001)
     * @param string|null $companyPubKey  Overrides the default company
     * @param string|null $companyPrivKey Overrides the default company
     *
     * @return string
     */
    protected function companyToken(
        $subject = '4c9184f37cff01bcdc32dc486ec36961:usr001',
        $companyPubKey = null,
        $companyPrivKey = null
    ) {
        if ((empty($companyPubKey)) || (empty($companyPrivKey))) {
            $companyPubKey  = '8b5fe9db84e338b424ed6d59da3254a0';
            $companyPrivKey = '4e37dae79456985ae0d27a67639cf335';
        }

        return Token::generateCompanyToken(
            $subject,
            $companyPubKey,
            $companyPrivKey
        );
    }

    /**
     * Generates a valid company token header to be used on requests.
     *
     * @param string|null $token Overrides the default token
     *
     * @return string
     */
    protected function companyTokenHeader($token = null) {
        if (empty($token)) {
            $token = $this->companyToken();
        }

        return sprintf('CompanyToken %s', $token);
    }

    /**
     * Generates a valid company token to be used on requests.
     *
     * @param string|null $credentialPubKey Overrides the default credentialPubKey value (4c9184f37cff01bcdc32dc486ec36961)
     * @param string|null $handlerPubKey    Overrides the default handler
     * @param string|null $handlerPrivKey   Overrides the default handler
     *
     * @return string
     */
    protected function credentialToken(
        $credentialPubKey = '4c9184f37cff01bcdc32dc486ec36961',
        $handlerPubKey = null,
        $handlerPrivKey = null
    ) {
        if ((empty($handlerPubKey)) || (empty($handlerPrivKey))) {
            $handlerPubKey  = 'ef970ffad1f1253a2182a88667233991';
            $handlerPrivKey = '213b83392b80ee98c8eb2a9fed9bb84d';
        }

        return Token::generateCredentialToken(
            $credentialPubKey,
            $handlerPubKey,
            $handlerPrivKey
        );
    }

    /**
     * Generates a valid credential token header to be used on requests.
     *
     * @param string|null $token Overrides the default token
     *
     * @return string
     */
    protected function credentialTokenHeader($token = null) {
        if (empty($token)) {
            $token = $this->credentialToken();
        }

        return sprintf('CredentialToken %s', $token);
    }
}
