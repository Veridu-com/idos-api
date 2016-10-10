<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Service;

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
        $this->uri        = '/1.0/companies/veridu-ltd/services';
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

        $this->assertSame(1321189817, $body['data'][0]['id']);
        $this->assertSame('idOS Scraper', $body['data'][0]['name']);
        $this->assertSame('https://handler.idos.io/1.0/scrape', $body['data'][0]['url']);
        $this->assertSame('ef970ffad1f1253a2182a88667233991', $body['data'][0]['public']);
        $this->assertSame(1, $body['data'][0]['access']);
        $this->assertTrue($body['data'][0]['enabled']);
        $this->assertEquals(['idos:source.facebook.added'], $body['data'][0]['listens']);
        $this->assertEquals(['idos:scraper.facebook.completed'], $body['data'][0]['triggers']);

        $this->assertSame(517015180, $body['data'][1]['id']);
        $this->assertSame('idOS Data Mapper', $body['data'][1]['name']);
        $this->assertSame('https://data-mapper.idos.io', $body['data'][1]['url']);
        $this->assertSame('8c178e650645a1f2a0c7de98757373b6', $body['data'][1]['public']);
        $this->assertSame(1, $body['data'][1]['access']);
        $this->assertTrue($body['data'][1]['enabled']);
        $this->assertEquals(['idos:scraper.facebook.completed'], $body['data'][1]['listens']);
        $this->assertEquals(['idos:data-mapper.facebook.completed'], $body['data'][1]['triggers']);

        $this->assertSame(1860914067, $body['data'][2]['id']);
        $this->assertSame('idOS Overall Model', $body['data'][2]['name']);
        $this->assertSame('https://overall.idos.io', $body['data'][2]['url']);
        $this->assertSame('043578887a8013e3805a789927b0fbf2', $body['data'][2]['public']);
        $this->assertSame(1, $body['data'][2]['access']);
        $this->assertTrue($body['data'][2]['enabled']);
        $this->assertEquals(
            [
                'idos:feature-extractor.facebook.completed',
                'idos:feature-extractor.twitter.completed',
                'idos:feature-extractor.linkedin.completed'
            ],
            $body['data'][2]['listens']
        );
        $this->assertEquals(['idos:overall.completed'], $body['data'][2]['triggers']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'service/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }
}
