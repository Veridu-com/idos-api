<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\Update\Profile;

use App\Exception\AppException;

/**
 * Candidate update exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class CandidateException extends AppException {
}
