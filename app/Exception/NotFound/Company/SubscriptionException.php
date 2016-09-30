<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Exception\NotFound\Company;

use App\Exception\NotFound;

/**
 * Subscription not found exception.
 *
 * @apiEndpointResponse 500 schema/error.json
 */
class SubscriptionException extends NotFound {
}