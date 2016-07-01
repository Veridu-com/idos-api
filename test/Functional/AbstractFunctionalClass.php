<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional;

use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Slim\App;
//schema
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;

abstract class AbstractFunctionalClass extends \PHPUnit_Framework_TestCase {

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

    protected function validateSchema($entity, $schemaName, $bodyResponse) {
        $resolver = new RefResolver(new UriRetriever(), new UriResolver());
        $schema   = $resolver->resolve(
            sprintf(
                'file://' . __DIR__ . '/../../schema/%s/%s.json',
                strtolower($entity),
                $schemaName
            )
        );
        $validator = new Validator();

        $validator->check(
            $bodyResponse,
            $schema
        );

        if(!$validator->isValid())
            $this->getSchemaErrors($validator);

        return $validator->isValid();
    }

    protected function getSchemaErrors($validator) {
        foreach ($validator->getErrors() as $error)
            print_r(sprintf("[%s] %s\n", $error['property'], $error['message']));
    }
}
