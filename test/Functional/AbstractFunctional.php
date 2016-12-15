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
    protected static $app;

    /**
     * Phinx Application Instance.
     *
     * @var Phinx\Console\PhinxApplication
     */
    protected static $phinxApp;

    /**
     * Phinx TextWrapper Instance.
     *
     * @var Phinx\Wrapper\TextWrapper
     */
    protected static $phinxTextWrapper;

    /**
     * SQL Database Connection.
     *
     * @var Illuminate\Database\Connection
     */
    protected static $sqlConnection;

    /**
     * NoSQL Database Connection.
     *
     * @var callable
     */
    protected static $noSqlConnection;

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
        if (! self::$app) {
            $app = new App(
                ['settings' => $GLOBALS['appSettings']]
            );

            require_once __ROOT__ . '/../config/dependencies.php';
            require_once __ROOT__ . '/../config/middleware.php';
            require_once __ROOT__ . '/../config/handlers.php';
            require_once __ROOT__ . '/../config/routes.php';
            require_once __ROOT__ . '/../config/listeners.php';

            self::$app = $app;
        }

        if (! self::$phinxApp) {
            $phinxApp = new PhinxApplication();

            self::$phinxApp = $phinxApp;
        }

        if (! self::$phinxTextWrapper) {
            $phinxTextWrapper = new TextWrapper(self::$phinxApp);
            $phinxTextWrapper->setOption('configuration', 'phinx.yml');
            $phinxTextWrapper->setOption('parser', 'YAML');
            $phinxTextWrapper->setOption('environment', 'testing');
            $phinxTextWrapper->getRollback('testing', 0);
            $phinxTextWrapper->getMigrate();
            $phinxTextWrapper->getSeed();

            self::$phinxTextWrapper = $phinxTextWrapper;
        }

        if (! self::$sqlConnection) {
            self::$sqlConnection = self::$app->getContainer()->get('sql');
        }

        if (! self::$noSqlConnection) {
            self::$noSqlConnection = self::$app->getContainer()->get('nosql');
        }
    }

    /**
     * Starts a SQL database transaction before each test.
     */
    protected function setUp() {
        self::$sqlConnection->beginTransaction();
    }

    /**
     * Rollback the SQL database transaction.
     */
    protected function tearDown() {
        self::$sqlConnection->rollback();
    }

    /**
     * Load all the dependencies for the aplication.
     *
     * @return Slim\App $app
     */
    protected function getApp() : App {
        return self::$app;
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

        if ($response->getStatusCode() >= 400) {
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
            'SCRIPT_NAME'    => '/index.php'
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
        $userName = 'f67b96dcf96b49d713a520ce9f54053c',
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
     * Generates a valid identity token to be used on requests.
     *
     * @param string|null $identityPublicKey Overrides the default identity
     * @param string|null $identityPrivKey   Overrides the default identity
     *
     * @return string
     */
    protected function identityToken(
        $identityPublicKey = '5d41402abc4b2a76b9719d911017c592',
        $identityPrivKey = '7d793037a0760186574b0282f2f435e7'
    ) {
        return Token::generateIdentityToken(
            $identityPublicKey,
            $identityPrivKey
        );
    }

    /**
     * Generates a valid identity token header to be used on requests.
     *
     * @param string|null $token Overrides the default token
     *
     * @return string
     */
    protected function identityTokenHeader($token = null) {
        if (empty($token)) {
            $token = $this->identityToken();
        }

        return sprintf('IdentityToken %s', $token);
    }

    /**
     * Generates a valid identity token to be used on requests.
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
            $handlerPubKey  = 'b16c931c061e14af275bd2c86d3cf48d';
            $handlerPrivKey = '81197557e9117dfd6f16cb72a2710830';
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

    public function combinatorics(array $list, int $start, int $end, callable $callback, array $combinations = []) {
        if ($end === 0) {
            return $callback($combinations);
        }

        for ($i = $start; $i <= count($list) - $end; $i++) {
            if (in_array($list[$i], $combinations)) {
                continue;
            }

            $combinations[] = $list[$i];
            $this->combinatorics($list, $start + 1, $end - 1, $callback, $combinations);
            array_pop($combinations);
        }
    }
}
