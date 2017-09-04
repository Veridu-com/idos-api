<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Test\Functional\AbstractFunctional;

abstract class AbstractRawFunctional extends AbstractFunctional {
    protected function populateDb(): void {
        /*$response = $this->process(
            $this->createRequest(
                $this->createEnvironment(
                    [
                        'REQUEST_URI'        => '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/raw',
                        'REQUEST_METHOD'     => 'DELETE',
                        'HTTP_CONTENT_TYPE'  => 'application/json',
                        'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                    ]
                )
            )
        );*/

        $fileSystem = self::$app->getContainer()->get('fileSystem');
        $fileSystem('raw')->deleteDir('facebook');

        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/raw',
                'REQUEST_METHOD'     => 'PUT',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 1321189817,
                        'collection' => 'rawTest1',
                        'data'       => ['test' => 'data1']
                    ]
                )
            )
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 1321189817,
                        'collection' => 'rawTest2',
                        'data'       => ['test' => 'data2']
                    ]
                )
            )
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 1321189817,
                        'collection' => 'rawTest3',
                        'data'       => ['test' => 'data3']
                    ]
                )
            )
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 517015180,
                        'collection' => 'rawTestX',
                        'data'       => ['test' => 'dataX']
                    ]
                )
            )
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 1041008870,
                        'collection' => 'rawTest1',
                        'data'       => ['test' => 'data4']
                    ]
                )
            )
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 1041008870,
                        'collection' => 'rawTest2',
                        'data'       => ['test' => 'data5']
                    ]
                )
            )
        );

        $response = $this->process(
            $this->createRequest(
                $environment,
                json_encode(
                    [
                        'source_id'  => 1041008870,
                        'collection' => 'rawTest3',
                        'data'       => ['test' => 'data6']
                    ]
                )
            )
        );
    }
}
