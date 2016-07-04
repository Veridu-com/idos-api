<?php
/**
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

abstract class AbstractFunctional extends \PHPUnit_Framework_TestCase {
    protected $schemaErrors;

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
        $app = new App(
            ['settings' => $GLOBALS['appSettings']]
        );

        require_once __ROOT__ . '/../config/dependencies.php';

        require_once __ROOT__ . '/../config/middleware.php';

        require_once __ROOT__ . '/../config/handlers.php';

        require_once __ROOT__ . '/../config/routes.php';

        return $app;
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
