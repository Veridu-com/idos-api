<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Helper;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Defuse\Crypto\KeyProtectedByPassword;

/**
 * Vault Class.
 */
class Vault {
    /**
     * Key instance used to encrypt/decrypt.
     *
     * @var \Defuse\Crypto\Key
     */
    private $key;

    /**
     * Class constructor.
     *
     * @param string $encodedKey
     * @param string $secureKey
     *
     * @return void
     */
    public function __construct(string $encodedKey, string $secureKey = '') {
        if ($secureKey === '') {
            $this->key = Key::loadFromAsciiSafeString($encodedKey);

            return;
        }

        $protectedKey = KeyProtectedByPassword::loadFromAsciiSafeString($encodedKey);
        $this->key    = $protectedKey->unlockKey($secureKey);
    }

    /**
     * Encrypts a plain text into cipher text.
     *
     * @param string $plainText
     *
     * @return string
     */
    public function lock(string $plainText) : string {
        if ($this->key === null) {
            return $plainText;
        }

        return Crypto::encrypt($plainText, $this->key);
    }

    /**
     * Decrypts a cipher text into plain text.
     *
     * @param string $cipherText
     *
     * @return string|null
     */
    public function unlock(string $cipherText) : ?string {
        if ($this->key === null) {
            return $cipherText;
        }

        try {
            return Crypto::decrypt($cipherText, $this->key);
        } catch (WrongKeyOrModifiedCiphertextException $exception) {
            return null;
        }
    }
}
