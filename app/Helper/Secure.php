<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Helper;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\WrongKeyOrModifiedCiphertextException;

/**
 * Secure Class.
 */
class Secure {
    /**
     * Key instance used to encrypt/decrypt.
     *
     * @var Defuse\Crypto\Key
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
    public function __construct(string $encodedKey, string $secureKey) {
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
        return Crypto::encrypt($plainText, $this->key);
    }

    /**
     * Decrypts a cipher text into plain text.
     *
     * @param string $cipherText
     *
     * @return string
     */
    public function unlock(string $cipherText) : string {
        try {
            return Crypto::decrypt($cipherText, $this->key);
        } catch (WrongKeyOrModifiedCiphertextException $exception) {
            // What to do here..?
        }
    }
}
