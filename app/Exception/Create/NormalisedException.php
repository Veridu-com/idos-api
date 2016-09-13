<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Create;

use App\Exception\AppException;

/**
 * Normalised create exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class NormalisedException extends AppException {
}
