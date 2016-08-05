<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Extension;

use App\Entity\EntityInterface;

/**
 * Trait to add secure fields support.
 */
trait SecureFields {
    /**
     * {@inheritdoc}
     */
    protected function setAttribute(string $key, $value) : EntityInterface {
        parent::setAttribute($key, $value);
        if ((isset($this->secure)) && (in_array($key, $this->secure))) {
            if (strpos((string) $value, 'secure:') === false) {
                $this->attributes[$key] = sprintf(
                    'secure:%s',
                    // $this->secure->lock($value)
                    $value
                );
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttribute(string $key) {
        $value = parent::getAttribute($key);
        if ((isset($this->secure)) && (in_array($key, $this->secure))) {
            if (strpos((string) $value, 'secure:') === 0) {
                $value = substr($value, 7);
                // $value = $this->secure->unlock($value);
            }
        }

        return $value;
    }
}
