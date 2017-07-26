<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Upsert\Profile;

use App\Exception\AppException;

/**
 * Review update exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class ReviewException extends AppException {
}
