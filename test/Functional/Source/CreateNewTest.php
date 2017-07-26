<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Source;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class CreateNewTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresUserToken,
        Traits\RequiresCredentialToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'POST';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/sources';

        // clears file storage
        $fileSystem = self::$app->getContainer()->get('fileSystem');
        $fileSystem('source')->deleteDir('1');
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'twitter',
            'tags' => [
                'access_token' => 'token'
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
        $this->assertSame($data['name'], $body['data']['name']);
        $this->assertEquals($data['tags'], $body['data']['tags']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('source/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testSuccessFile() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
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
        $this->assertSame($data['name'], $body['data']['name']);

        unset($data['tags']['contents']);
        $data['tags']['file_size'] = 8574;
        $data['tags']['file_sha1'] = 'c0bac14ef6655a689f2e73213dde2d25ad22e26c';
        $this->assertEquals($data['tags'], $body['data']['tags']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('source/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testSuccessEmailOTP() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'email',
            'tags' => [
                'otp_check' => true,
                'email'     => 'test@example.com'
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
        $this->assertSame($data['name'], $body['data']['name']);

        unset($data['tags']['otp_check']);
        $data['tags']['otp_verified'] = false;
        $this->assertEquals($data['tags'], $body['data']['tags']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('source/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testSuccessSMSOTP() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'phone',
            'tags' => [
                'otp_check' => true,
                'phone'     => '+0000000000'
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
        $this->assertSame($data['name'], $body['data']['name']);

        unset($data['tags']['otp_check']);
        $data['tags']['otp_verified'] = false;
        $this->assertEquals($data['tags'], $body['data']['tags']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('source/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testSuccessCRA() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'tracesmart',
            'tags' => [
                'cra_check' => true
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
        $this->assertSame($data['name'], $body['data']['name']);

        $this->assertArrayHasKey('cra_reference', $body['data']['tags']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('source/createNew.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedFile1() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'passport',
            'tags' => [
                'extension' => 'txt',
                'contents'  => str_repeat('.', 4194305)
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedFile2() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'passport',
            'tags' => [
                'extension' => 'txt',
                'contents'  => str_repeat('.', 40)
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedFile3() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'passport',
            'tags' => [
                'contents' => base64_encode(file_get_contents(__RSRC__ . '/passport.png'))
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedEmailOTP1() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'email',
            'tags' => [
                'otp_check' => true
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedEmailOTP2() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'email',
            'tags' => [
                'otp_check' => true,
                'email'     => 'invalid@email'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedSMSOTP1() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'phone',
            'tags' => [
                'otp_check' => true
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testFailedSMSOTP2() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'phone',
            'tags' => [
                'otp_check' => true,
                'phone'     => '+123456'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testEmptyName() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => '',
            'tags' => [
                'otp_check' => 'email'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);

        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }

    public function testInvalidName() {
        $environment = $this->createEnvironment(
            [
                'HTTP_CONTENT_TYPE'  => 'application/json',
                'HTTP_AUTHORIZATION' => $this->userTokenHeader()
            ]
        );

        $data = [
            'name' => 'this name is too long and shall not pass this validation',
            'tags' => [
                'otp_check' => 'email'
            ]
        ];

        $request = $this->createRequest(
            $environment,
            json_encode($data)
        );

        $response = $this->process($request);

        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertFalse($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema('error.json', json_decode((string) $response->getBody())),
            $this->schemaErrors
        );
    }
}
