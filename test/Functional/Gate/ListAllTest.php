<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Functional\Gate;

use Test\Functional\AbstractFunctional;
use Test\Functional\Traits;

class ListAllTest extends AbstractFunctional {
    use Traits\RequiresAuth,
        Traits\RequiresCredentialToken,
        Traits\RejectsUserToken,
        Traits\RejectsIdentityToken;

    protected function setUp() {
        parent::setUp();

        $this->httpMethod = 'GET';

        $this->uri = '/1.0/profiles/f67b96dcf96b49d713a520ce9f54053c/gates';
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
        $body     = json_decode((string) $response->getBody(), true);

        $this->assertNotEmpty($body);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($body['status']);

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testFilters() {
        $key = 'slug';
        $filterableKeys = [
            'creator.name' => [
                [
                    'value' => 'id*',
                    'results' => ['gate-one', 'gate-two']
                ],
            ],

            'name' => [
                [
                    'value' => '*one',
                    'results' => ['gate-one']
                ],
            ],

            'slug' => [
                [
                    'value' => '*one',
                    'results' => ['gate-one']
                ],
            ],
        ];

        for ($i = 1; $i < count($filterableKeys); $i++) {
            $this->combinatorics(array_keys($filterableKeys), 0, $i,
            function (array $filters) use ($key, $filterableKeys) {
                $queryString = [];
                $possibleResults = [];
                foreach ($filters as $filter) {
                    $possibleValues = $filterableKeys[$filter];

                    foreach ($possibleValues as $possibleValue) {
                        if (count($possibleResults) > 0) {
                            $possibleResults = array_intersect($possibleResults, $possibleValue['results']);
                        } else {
                            $possibleResults = array_merge($possibleResults, $possibleValue['results']);
                        }

                        $queryString[] = $filter . '=' . $possibleValue['value'];
                    }
                }

                $request = $this->createRequest(
                    $this->createEnvironment(
                        [
                            'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                            'QUERY_STRING'       => implode('&', $queryString)
                        ]
                    )
                );

                $response = $this->process($request);
                $this->assertSame(200, $response->getStatusCode());

                $body = json_decode((string) $response->getBody(), true);
                $this->assertNotEmpty($body);
                $this->assertTrue($body['status']);

                foreach ($body['data'] as $entity) {
                    $this->assertContains($entity[$key], $possibleResults);
                }

                $this->assertTrue(
                    $this->validateSchema(
                        'gate/listAll.json',
                        json_decode((string) $response->getBody())
                    ),
                    $this->schemaErrors
                );
            });
        }
    }

    public function testNameFilter() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'name=*one'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(1, $body['data']);

        foreach ($body['data'] as $entity) {
            $this->assertContains($entity['name'], ['Gate one']);
            $this->assertContains($entity['slug'], ['gate-one']);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testNameFilterMultiple() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'name=Gate*'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        foreach ($body['data'] as $entity) {
            $this->assertContains($entity['name'], ['Gate one', 'Gate two']);
            $this->assertContains($entity['slug'], ['gate-one', 'gate-two']);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testCreatorNameFilter() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'creator:name=idOS Scraper'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        foreach ($body['data'] as $entity) {
            $this->assertContains($entity['name'], ['Gate one', 'Gate two']);
            $this->assertContains($entity['slug'], ['gate-one', 'gate-two']);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testCreatorNameFilterMultiple() {
        $request = $this->createRequest(
            $this->createEnvironment(
                [
                    'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                    'QUERY_STRING'       => 'creator:name=id*'
                ]
            )
        );

        $response = $this->process($request);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode((string) $response->getBody(), true);
        $this->assertNotEmpty($body);
        $this->assertTrue($body['status']);
        $this->assertCount(2, $body['data']);

        foreach ($body['data'] as $entity) {
            $this->assertContains($entity['name'], ['Gate one', 'Gate two']);
            $this->assertContains($entity['slug'], ['gate-one', 'gate-two']);
        }

        /*
         * Validates Response using the Json Schema.
         */
        $this->assertTrue(
            $this->validateSchema(
                'gate/listAll.json',
                json_decode((string) $response->getBody())
            ),
            $this->schemaErrors
        );
    }

    public function testOrdering() {
        $orderableKeys = [
            'name',
            'slug',
            'pass',
            'created_at',
            'updated_at'
        ];

        foreach (['ASC', 'DESC'] as $sort) {
            foreach ($orderableKeys as $orderableKey) {
                $request = $this->createRequest(
                    $this->createEnvironment(
                        [
                            'HTTP_AUTHORIZATION' => $this->credentialTokenHeader(),
                            'QUERY_STRING'       => 'filter:order=' . $orderableKey . '&filter:sort=' . $sort
                        ]
                    )
                );

                $response = $this->process($request);
                $this->assertSame(200, $response->getStatusCode());

                $body = json_decode((string) $response->getBody(), true);
                $this->assertNotEmpty($body);
                $this->assertTrue($body['status']);
                $this->assertCount(2, $body['data']);

                $keys = [];

                if (strpos(':', $orderableKey) !== false) {
                    $orderableKey = explode(':', $orderableKey);
                } else {
                    foreach ($body['data'] as $entity) {
                        $keys[] = isset($entity[$orderableKey]) ? $entity[$orderableKey] : null;
                    }

                    $orderedKeys = $keys;

                    if ($sort === 'ASC') {
                        sort($orderedKeys);
                    } else {
                        rsort($orderedKeys);
                    }

                    if ($orderedKeys !== $keys) {
                        $this->fail('Failed asserting correctly ordered elements (' . $orderableKey . ', ' . $sort . ')');
                    }
                }

                /*
                 * Validates Response using the Json Schema.
                 */
                $this->assertTrue(
                    $this->validateSchema(
                        'gate/listAll.json',
                        json_decode((string) $response->getBody())
                    ),
                    $this->schemaErrors
                );
            }
        }
    }
}
