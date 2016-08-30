<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

// namespace Test\Functional\Tag;

// use Slim\Http\Response;
// use Slim\Http\Uri;
// use Test\Functional\AbstractFunctional;
// use Test\Functional\Traits\HasAuthCredentialToken;
// use Test\Functional\Traits\HasAuthMiddleware;

// class CreateNewTest extends AbstractFunctional {
//     use HasAuthMiddleware;
//     use HasAuthCredentialToken;

//     protected function setUp() {
//         $this->httpMethod = 'POST';
//         $this->uri        = '/1.0/profiles/9fd9f63e0d6487537569075da85a0c7f2/tags';
//     }

//     public function testSuccess() {
//         $environment = $this->createEnvironment(
//             [
//                 'HTTP_CONTENT_TYPE' => 'application/json',
//                 'QUERY_STRING'      => 'credentialPrivKey=2c17c6393771ee3048ae34d6b380c5ec'
//             ]
//         );

//         $request = $this->createRequest(
//             $environment,
//             json_encode(
//                 [
//                     'name' => 'Tag Test',
//                     'slug' => 'tag-test'
//                 ]
//             )
//         );

//         $response = $this->process($request);

//         $body = json_decode($response->getBody(), true);

//         $this->assertNotEmpty($body);

//         $this->assertEquals(201, $response->getStatusCode());

//         $this->assertTrue($body['status']);
//         $this->assertSame('Tag Test', $body['data']['name']);
//         $this->assertSame('tag-test', $body['data']['slug']);
//         /*
//          * Validates Json Schema against Json Response'
//          */
//         $this->assertTrue(
//             $this->validateSchema(
//                 'tag/createNew.json',
//                 json_decode($response->getBody())
//             ),
//             $this->schemaErrors
//         );

//     }
// }
