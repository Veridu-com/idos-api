<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class GetOneTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RequiresCredentialToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $fileSystem = self::$app->getContainer()->get('fileSystem');
        $fileSystem('source')->deleteDir('1');

        $this->httpMethod = 'GET';
        $this->populate(
            '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources',
            'GET',
            [
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $this->entity = $this->getRandomEntity();
        $this->uri    = sprintf('/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/%s', $this->entity['id']);
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->userTokenHeader()
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'source/getOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFileSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader(),
                'REQUEST_URI'        => '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources',
                'REQUEST_METHOD'     => 'POST'
            ]
        );

        $data = [
            'name' => 'passport',
            'tags' => [
                'mime'      => 'image/png',
                'extension' => 'png',
                'contents'  => base64_encode(file_get_contents(__RSRC__ . '/passport.png'))
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(201, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        $this->uri = sprintf('/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/%s', $body['data']['id']);

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->userTokenHeader(),
                    'QUERY_STRING'       => 'includePicture=true'
                ]
            )
        );
        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertNotEmpty($body['data']['tags']['contents']);
        $this->assertSame($data['tags']['contents'], $body['data']['tags']['contents']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'source/getOne.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNotFound() {
        $this->uri = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources/9839283298';

        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->userTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'error.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
