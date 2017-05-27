<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception;

/**
 * CORS Exception.
 *
 * @apiEndpointResponse 401 schema/error.json
 *
 * @see \App\Exception\AppException
 */
class CorsError extends AppException {
    /**
     * {@inheritdoc}
     */
    protected $code = 401;
    /**
     * {@inheritdoc}
     */
    protected $message = 'CORS Error';
}
