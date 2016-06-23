<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Exception;

/**
 * Base Application Exception.
 *
 * @apiStatusCode 500 Application Internal Error
 */
class AppException extends \Exception {
    /**
     * {@inheritdoc}
     */
    protected $code = 500;
    /**
     * {@inheritdoc}
     */
    protected $message = 'Application Internal Error.';
}
