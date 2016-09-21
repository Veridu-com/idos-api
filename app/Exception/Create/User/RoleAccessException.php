<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Create\User;

use App\Exception\AppException;

/**
 * RoleAccess create exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class RoleAccessException extends AppException {
}
