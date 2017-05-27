<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Db;

use App\Helper\Vault;
use Phinx\Seed\AbstractSeed;

abstract class AbstractExtendedSeed extends AbstractSeed {
    /**
     * Vault instance.
     *
     * @var \App\Helper\Vault
     */
    protected $vault = null;

    /**
     * {@inheritdoc}
     */
    protected function init() {
        require_once __DIR__ . '/../config/settings.php';

        $fileName = __DIR__ . '/../resources/secure.key';
        if (! is_file($fileName)) {
            throw new \RuntimeException('Secure key not found!');
        }

        $encoded = file_get_contents($fileName);
        if (empty($encoded)) {
            throw new \RuntimeException('Secure key could not be loaded!');
        }

        $this->vault = new Vault($encoded, $GLOBALS['appSettings']['secure']);
    }

    /**
     * Encrypts a value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function lock($value) : string {
        return sprintf('secure:%s', $this->vault->lock($value));
    }
}
