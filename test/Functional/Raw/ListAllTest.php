<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Raw;

use Test\Functional\Traits;

class ListAllTest extends AbstractRawFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';
        $this->uri        = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/raw';
        $this->populateDb();
    }

    public function testSuccess() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader()
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        $this->assertCount(7, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains(
                $raw['collection'],
                ['rawTest1', 'rawTest2', 'rawTest3', 'rawTestX', 'rawTest4', 'rawTest5', 'rawTest6']
            );
            $this->assertContains(
                $raw['data'],
                [
                    ['test' => 'data1'],
                    ['test' => 'data2'],
                    ['test' => 'data3'],
                    ['test' => 'dataX'],
                    ['test' => 'data4'],
                    ['test' => 'data5'],
                    ['test' => 'data6']
                ]
            );
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter1() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'collection=rawTest1'
                ]
            )
        );

        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        $this->assertCount(2, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains($raw['collection'], ['rawTest1']);
            $this->assertContains($raw['data'], [['test' => 'data1'], ['test' => 'data4']]);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter2() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'source:name=amazon'
                ]
            )
        );

        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        $this->assertCount(1, $body['data']);

        $this->assertContains($body['data'][0]['collection'], ['rawTestX']);
        $this->assertContains($body['data'][0]['data'], [['test' => 'dataX']]);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilter3() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'filter:order=latest&filter:limit=1&source:name=facebook'
                ]
            )
        );

        $response = $this->process($request);

        $this->assertSame(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);

        $this->assertCount(3, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains($raw['collection'], ['rawTest1', 'rawTest2', 'rawTest3']);
            $this->assertContains($raw['data'], [['test' => 'data4'], ['test' => 'data5'], ['test' => 'data6']]);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilterMultiple() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'collection=rawTest1,rawTest3'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(4, $body['data']);

        foreach ($body['data'] as $raw) {
            $this->assertContains($raw['collection'], ['rawTest1', 'rawTest3']);
            $this->assertContains(
                $raw['data'],
                [
                    ['test' => 'data1'],
                    ['test' => 'data3'],
                    ['test' => 'data4'],
                    ['test' => 'data6']
                ]
            );
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'raw/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
