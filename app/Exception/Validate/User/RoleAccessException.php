<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Validate\User;

use App\Exception\AppException;

/**
 * RoleAccess validate exception.
 *
 * @apiEndpointResponse 400 schema/error.json
 */
class RoleAccessException extends AppException {
}
