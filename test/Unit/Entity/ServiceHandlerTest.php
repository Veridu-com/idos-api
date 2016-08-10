<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\ServiceHandler;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class ServiceHandlerTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getAttributes() {
        return [
            'id'           => 1,
            'name'         => 'New Service Handler',
            'source'       => 'email',
            'location'     => 'url',
            'service_slug' => 'slug',
            'created_at'   => time(),
            'updated_at'   => time(),
        ];
    }

    public function testSerialize() {
        $abstractMock = $this->getMockBuilder(ServiceHandler::class)
            ->setMethods(null)
            ->setConstructorArgs([
                array_merge(
                    [
                        'companyId'    => 1,
                        'serviceId'    => 2,
                        'authUsername' => 'Auth Username',
                        'authPassword' => 'Auth Password'
                    ],
                    $this->getAttributes()
                ),
                $this->optimus
            ])
            ->getMockForAbstractClass();

        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('company_id', $array);
        $this->assertSame(1, $array['company_id']);
        $this->assertArrayHasKey('service_id', $array);
        $this->assertSame(2, $array['service_id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('New Service Handler', $array['name']);
        $this->assertArrayHasKey('service_slug', $array);
        $this->assertSame('slug', $array['service_slug']);
        $this->assertArrayHasKey('source', $array);
        $this->assertSame('email', $array['source']);
        $this->assertArrayHasKey('location', $array);
        $this->assertSame('secure:url', $array['location']);
        $this->assertArrayHasKey('auth_password', $array);
        $this->assertSame('Auth Password', $array['auth_password']);
        $this->assertArrayHasKey('auth_username', $array);
        $this->assertSame('Auth Username', $array['auth_username']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(ServiceHandler::class)
            ->setMethods(null)
            ->setConstructorArgs([
                array_merge(
                    [
                        'companyId'    => 1,
                        'serviceId'    => 2,
                        'authUsername' => 'Auth Username',
                        'authPassword' => 'Auth Password'
                    ],
                    $this->getAttributes()
                ),
                $this->optimus
            ])
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('New Service Handler', $array['name']);
        $this->assertArrayHasKey('service_slug', $array);
        $this->assertSame('slug', $array['service_slug']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('new-service-handler', $array['slug']);
        $this->assertArrayHasKey('source', $array);
        $this->assertSame('email', $array['source']);
        $this->assertArrayHasKey('location', $array);
        $this->assertSame('url', $array['location']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_int($array['updated_at']));
    }
}
