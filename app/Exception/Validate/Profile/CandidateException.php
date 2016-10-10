<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Validate\Profile;

use App\Exception\AppException;

/**
 * Candidate validate exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class CandidateException extends AppException {
}
