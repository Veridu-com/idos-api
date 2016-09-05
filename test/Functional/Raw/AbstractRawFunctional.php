<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\RequiresAuth;
use Test\Functional\Traits\RequiresCredentialToken;

abstract class AbstractRawFunctional extends AbstractFunctional {
    use RequiresAuth;
    use RequiresCredentialToken;

    protected function populateDb() {
        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/profiles/usr001/sources/1321189817/raw',
                'REQUEST_METHOD'     => 'DELETE',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $response = $this->process($this->createRequest($environment));

        $environment = $this->createEnvironment(
            [
                'REQUEST_URI'        => '/1.0/profiles/usr001/sources/1321189817/raw',
                'REQUEST_METHOD'     => 'POST',
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $response = $this->process($this->createRequest(
            $environment,
            json_encode(
                [
                    'collection' => 'raw-1',
                    'data' => 'data-1'
                ]
            )
        ));

        $response = $this->process($this->createRequest(
            $environment,
            json_encode(
                [
                    'collection' => 'raw-2',
                    'data' => 'data-2'
                ]
            )
        ));

        $response = $this->process($this->createRequest(
            $environment,
            json_encode(
                [
                    'collection' => 'raw-3',
                    'data' => 'data-3'
                ]
            )
        ));
    }
}
