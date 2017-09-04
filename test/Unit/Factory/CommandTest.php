<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Factory;

use App\Command\CommandInterface;
use App\Factory\Command;
use Test\Unit\AbstractUnit;

class CommandTest extends AbstractUnit {
    public function setUp() {
        $this->factory = new Command();
    }

    public function testCorrectInterface() {
        $commands = $this->getCommandNames();

        foreach ($commands as $command) {
            $this->assertInstanceOf(
                CommandInterface::class,
                $this->factory->create($command)
            );
        }
    }

    private function getCommandNames() {
        $commandPaths = glob(__DIR__ . '/../../../app/Command/*/*.php');
        $commands     = [];
        foreach ($commandPaths as $commandPath) {
            $matches = [];
            preg_match_all('/.*Command\/(.*)\/(.*).php/', $commandPath, $matches);

            $resource = $matches[1][0];
            $command  = $matches[2][0];

            $commands[] = sprintf('%s\\%s', $resource, $command);
        }

        return $commands;
    }
}
