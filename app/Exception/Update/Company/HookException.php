<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Update\Company;

use App\Exception\AppException;

/**
 * Hook update exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class HookException extends AppException {
}
