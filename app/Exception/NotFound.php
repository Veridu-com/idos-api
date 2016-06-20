<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Exception;

/**
 * Model Not Found Exception.
 *
 * @apiEndpointResponse 404 Error
 *
 * @see App\Exception\AppException
 */
class NotFound extends AppException {
    /**
     * {@inheritDoc}
     */
    protected $code = 404;
    /**
     * {@inheritDoc}
     */
    protected $message = 'Item not found.';
}
