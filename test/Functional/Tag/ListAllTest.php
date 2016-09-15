<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Tag;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class ListAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresIdentityToken,
        Traits\RejectsUserToken,
        Traits\RejectsCredentialToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/fd1fde2f31535a266ea7f70fdf224079/tags';
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader()
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
                'tag/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader(),
                    'QUERY_STRING'       => 'slug=%1'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertCount(1, $body['data']);

        foreach ($body['data'] as $tag) {
            $this->assertContains($tag['name'], ['User 2 Tag 1']);
            $this->assertContains($tag['slug'], ['user-2-tag-1']);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'tag/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilterMultiple() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->identityTokenHeader(),
                    'QUERY_STRING'       => 'slug=user-2%'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        foreach ($body['data'] as $tag) {
            $this->assertContains($tag['name'], ['User 2 Tag 1', 'User 2 Tag 2']);
            $this->assertContains($tag['slug'], ['user-2-tag-1', 'user-2-tag-2']);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'tag/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
