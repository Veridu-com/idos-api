<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Create\Profile;

use App\Exception\AppException;

/**
 * Gate create exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class GateException extends AppException {
}
