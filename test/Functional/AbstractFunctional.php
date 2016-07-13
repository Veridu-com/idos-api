<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional;

use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

abstract class AbstractFunctional extends \PHPUnit_Framework_TestCase {
    private $app;
    protected $schemaErrors;

    /**
     * entities populated via populate() method.
     *
     * @see self::populate()
     */
    protected $entities;

    /**
     * entity property of the test.
     */
    protected $entity;

    /**
     *  http method of the test.
     */
    protected $httpMethod;

    /**
     *  uri property of the test.
     */
    protected $uri;

    public static function setUpBeforeClass() {
        $phinxApp         = new PhinxApplication();
        $phinxTextWrapper = new TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'testing');
        $phinxTextWrapper->getRollback('testing', 0);
        $phinxTextWrapper->getMigrate();
        $phinxTextWrapper->getSeed();
    }

    public static function tearDownAfterClass() {
        $phinxApp         = new PhinxApplication();
        $phinxTextWrapper = new TextWrapper($phinxApp);
        $phinxTextWrapper->setOption('configuration', 'phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'testing');
        $phinxTextWrapper->getRollback('testing', 0);
    }

    protected function getApp() {
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

    protected function process($request) {
        return $this->getApp()->process($request, new Response());
    }

    /**
     *  Populates the $entities property of the instance querying the given URI.
     *  
     *  @param string $uri URI to be queried
     *  @param string $method URI to be queried
     *
     *  @return array $entities
     */
    protected function populate(string $uri, string $method = 'GET') : array {
        $environment = $this->createEnvironment([
            'REQUEST_URI'    => $uri,
            'REQUEST_METHOD' => $method
        ]);

        $request    = $this->createRequest($environment);
        $response   = $this->process($request);
        $body       = json_decode($response->getBody(), true);

        $this->entities = $body['data'];

        return $this->entities;
    }

    protected function getRandomEntity($index = false) {
        if (! $this->entities) {
            throw new \RuntimeException('Test instance not populated, call populate() method before calling getRandomEntity() method.');
        }
        if ($index === false) {
            $index = mt_rand(0, (sizeof($this->entities) - 1));
        }

        return $this->entities[$index];
    }

    protected function createEnvironment(array $options = []) {
        $defaults = [
            'REQUEST_URI'    => $this->uri,
            'REQUEST_METHOD' => $this->httpMethod,
            'SCRIPT_NAME'    => '/index.php',
            'QUERY_STRING'   => 'companyPrivKey=4e37dae79456985ae0d27a67639cf335'
        ];

        return Environment::mock(array_merge($defaults, $options));
    }

    protected function createRequest(Environment $environment) {
        return new Request(
            $environment->get('REQUEST_METHOD'),
            Uri::createFromEnvironment($environment),
            Headers::createFromEnvironment($environment),
            [],
            $environment->all(),
            new RequestBody()
        );
    }

    protected function validateSchema($schemaFile, $bodyResponse) {
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

        if(! $validator->isValid())
            $this->getSchemaErrors($validator);

        return $validator->isValid();
    }

    protected function getSchemaErrors($validator) {
        $this->schemaErrors = null;
        foreach ($validator->getErrors() as $error)
            $this->schemaErrors .= sprintf("[%s] %s\n", $error['property'], $error['message']);
    }
}
