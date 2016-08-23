<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Tag;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCredentialToken;
use Test\Functional\Traits\HasAuthMiddleware;

class ListAllTest extends AbstractFunctional {
    use HasAuthMiddleware;
    use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/9fd9f63e0d6487537569075da85a0c7f2/tags';
    }

    public function testSuccess() {
        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'credentialPrivKey=2c17c6393771ee3048ae34d6b380c5ec'
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'tag/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter() {
        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'tags=user 2 tag 1&credentialPrivKey=2c17c6393771ee3048ae34d6b380c5ec'
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        $this->assertEquals(1, count($body['data']));

        foreach($body['data'] as $tag) {
            $this->assertContains($tag['name'], ['user-2-tag-1']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'tag/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilterMultiple() {
        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'tags=User 2 tag-1,user-2-tag-2&credentialPrivKey=2c17c6393771ee3048ae34d6b380c5ec'
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        $this->assertEquals(2, count($body['data']));

        foreach($body['data'] as $tag) {
            $this->assertContains($tag['name'], ['user-2-tag-1', 'user-2-tag-2']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'tag/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

}
