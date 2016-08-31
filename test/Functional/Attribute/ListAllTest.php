<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Functional\Attribute;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits\HasAuthCredentialToken;
use Test\Functional\Traits\HasAuthMiddleware;

class ListAllTest extends AbstractFunctional {
    //use HasAuthMiddleware;
    //use HasAuthCredentialToken;

    protected function setUp() {
        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/fd1fde2f31535a266ea7f70fdf224079/attributes';
    }

    public function testSuccess() {
        $environment = $this->createEnvironment(
            [
                'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
            ]
        );

        $request  = $this->createRequest($environment);
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
                'attribute/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter() {
        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'names=user2Attribute1&credentialToken=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJlZjk3MGZmYWQxZjEyNTNhMjE4MmE4ODY2NzIzMzk5MSIsInN1YiI6IjRjOTE4NGYzN2NmZjAxYmNkYzMyZGM0ODZlYzM2OTYxIn0.oeiD9R7FlnMBiDW3UClRO39nvbMM-TTZkyedYaSysCc'
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        $this->assertEquals(1, count($body['data']));

        foreach($body['data'] as $attribute) {
            $this->assertContains($attribute['name'], ['user2Attribute1']);
            $this->assertContains($attribute['value'], ['value-3']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'attribute/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilterMultiple() {
        $request = $this->createRequest($this->createEnvironment([
            'QUERY_STRING' => 'names=user2Attribute1,user2Attribute2&credentialToken=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJlZjk3MGZmYWQxZjEyNTNhMjE4MmE4ODY2NzIzMzk5MSIsInN1YiI6IjRjOTE4NGYzN2NmZjAxYmNkYzMyZGM0ODZlYzM2OTYxIn0.oeiD9R7FlnMBiDW3UClRO39nvbMM-TTZkyedYaSysCc'
        ]));

        $response = $this->process($request);
        $body     = json_decode($response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        $this->assertEquals(2, count($body['data']));

        foreach($body['data'] as $attribute) {
            $this->assertContains($attribute['name'], ['user2Attribute1', 'user2Attribute2']);
            $this->assertContains($attribute['value'], ['value-3', 'value-4']);
        }

        /*
         * Validates Json Schema against Json Response
         */
        $this->assertTrue(
            $this->validateSchema(
                'attribute/listAll.json',
                json_decode($response->getBody())
            ),
            $this->schemaErrors
        );
    }

}
