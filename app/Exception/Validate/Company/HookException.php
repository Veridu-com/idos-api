<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Validate\Company;

use App\Exception\AppException;

/**
 * Hook validate exception.
 *
 * @apiEndpointResponse 400 schema/error.json
 */
class HookException extends AppException {
}
