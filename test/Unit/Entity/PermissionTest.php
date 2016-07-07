<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Permission;

class PermissionTest extends \PHPUnit_Framework_TestCase {
    public function testSerialize() {
        $array = [
            'id'          => 0,
            'routeName'   => 'companies:listAll',
            'created_at'  => time()
        ];

        $abstractMock = $this->getMockBuilder(Permission::class)
            ->setMethods(null)
            ->setConstructorArgs(['attributes' => $array])
            ->getMockForAbstractClass();

        $array = $abstractMock->serialize();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame(0, $array['id']);

        $this->assertArrayHasKey('routeName', $array);
        $this->assertSame('companies:listAll', $array['routeName']);
        
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Permission::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'          => 0,
                        'routeName' => 'settings:listAll',
                        'created_at'  => time()
                    ]
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();

        $this->assertArrayHasKey('routeName', $array);
        $this->assertSame('settings:listAll', $array['routeName']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }
}
