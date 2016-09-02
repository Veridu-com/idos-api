<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

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
            'id'         => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }

    public function testSerialize() {
        $abstractMock = $this->getMockBuilder(ServiceHandler::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                array_merge(
                    [
                        'companyId'    => 1,
                        'serviceId'    => 2,
                        'listens'      => ['listen1', 'listen2'],
                        'authUsername' => 'Auth Username',
                        'authPassword' => 'Auth Password'
                    ],
                    $this->getAttributes()
                ),
                $this->optimus
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);

        $this->assertArrayHasKey('company_id', $array);
        $this->assertSame(1, $array['company_id']);

        $this->assertArrayHasKey('service_id', $array);
        $this->assertSame(2, $array['service_id']);

        $this->assertArrayHasKey('listens', $array);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals(['listen1', 'listen2'], json_decode($array['listens']));

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
            ->setConstructorArgs(
                [
                array_merge(
                    [
                        'companyId' => 1,
                        'serviceId' => 2,
                        'listens'   => ['listen1', 'listen2']
                    ],
                    $this->getAttributes()
                ),
                $this->optimus
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();

        $this->assertArrayHasKey('listens', $array);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals(['listen1', 'listen2'], $array['listens']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));

        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_int($array['updated_at']));
    }
}
