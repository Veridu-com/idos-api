<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace Test\Unit\Middleware;

use App\Exception\AppException;
use App\Exception\NotFound;
use App\Repository\CompanyInterface;
use App\Repository\CredentialInterface;
use App\Repository\UserInterface;
use Lcobucci\JWT\Parser as JWTParser;
use Lcobucci\JWT\Signer\Hmac\Sha256 as JWTSigner;
use Lcobucci\JWT\ValidationData as JWTValidation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthTest extends AbstractUnit {

}
