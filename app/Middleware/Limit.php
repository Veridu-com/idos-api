<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\AppException;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Stash\Item;

/**
 * Limit Middleware.
 *
 * Scope: Route.
 * Enforces request limits and adds usage details to response headers.
 *
 * FIXME This needs to be updated as Stash has been removed..
 */
class Limit implements MiddlewareInterface {
    /**
     * Dependency Container.
     *
     * @var \Interop\Container\ContainerInterface
     */
    private $container;
    /**
     * Soft Limit (emits alert).
     *
     * @var int
     */
    private $softLimit;
    /**
     * Hard Limit (blocks request).
     *
     * @var int
     */
    private $hardLimit;
    /**
     * Limit Type (Key or User).
     *
     * @var int
     */
    private $limitType;

    /**
     * Key-based limit control.
     *
     * @const KEYLIMIT
     */
    const KEYLIMIT = 0x00;
    /**
     * User-based limit control.
     *
     * @const USRLIMIT
     */
    const USRLIMIT = 0x01;

    /**
     * Class constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param int                                   $softLimit
     * @param int                                   $hardLimit
     * @param int                                   $limitType
     *
     * @return void
     */
    public function __construct(
        ContainerInterface $container,
        int $softLimit,
        int $hardLimit,
        int $limitType = self::KEYLIMIT
    ) {
        $this->container = $container;
        $this->softLimit = $softLimit;
        $this->hardLimit = $hardLimit;
        $this->limitType = $limitType;
    }

    /**
     * Middleware execution, forces request limitting based on usage.
     *
     * @apiEndpointRespHeader X-Rate-Limit-Limit 1
     * @apiEndpointRespHeader X-Rate-Limit-Remaining 1
     * @apiEndpointRespHeader X-Rate-Limit-Reset 1
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     *
     * @throws App\Exception\AppException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) : ResponseInterface {
        $key = $request->getAttribute('key');
        if ($this->limitType == self::KEYLIMIT)
            // The limit is based on the key / route identifier
            $controlKey = sprintf(
                '/limit/key/%d/%s',
                $key->getId(),
                $request->getAttribute('route')->getIdentifier()
            );
        elseif ($this->limitType == self::USRLIMIT)
            // The limit is based on the key / user / route identifier
            $controlKey = sprintf(
                '/limit/user/%d/%s/%s',
                $key->getId(),
                $request->getAttribute('user')->getUserName(),
                $request->getAttribute('route')->getIdentifier()
            );

        $item = $this->container->get('cache')->getItem($controlKey);

        $limitControl = $item->get(Item::SP_VALUE, null);

        if (empty($limitControl))
            $limitControl = [
                'usage' => 0,
                'reset' => (time() + 3600)
            ];

        // Increase usage counter
        $limitControl['usage']++;

        $response = $response
            ->withHeader('X-Rate-Limit-Limit', $this->hardLimit)
            ->withHeader('X-Rate-Limit-Remaining', ($this->hardLimit - $limitControl['usage']))
            ->withHeader('X-Rate-Limit-Reset', $limitControl['reset']);

        if ($item->isMiss()) {
            // First request to be monitored
            $item->lock();
            $item->set($limitControl, 3600);

            return $next($request, $response);
        }

        // Above hard limit requests are logged and throw an Exception
        if ($limitControl['usage'] >= $this->hardLimit) {
            $log = $this->container->get('log');
            $log('LimitMiddleware')->notice(
                sprintf(
                    'Limit: over hard threhold: %s (%s %s) [%d]',
                    $key->getPublicKey(),
                    $request->getMethod(),
                    $request->getURI()->getPath(),
                    $this->limitType
                )
            );
            throw new AppException('429 Too Many Requests');
        }

        // Above soft limit requests are logged only
        if ($limitControl['usage'] >= $this->softLimit) {
            $log = $this->container->get('log');
            $log('LimitMiddleware')->notice(
                sprintf(
                    'Limit: over soft threshold: %s (%s %s) [%d]',
                    $key->getPublicKey(),
                    $request->getMethod(),
                    $request->getURI()->getPath(),
                    $this->limitType
                )
            );
        }

        $item->set($limitControl, ($limitControl['reset'] - time()));

        return $next($request, $response);
    }
}
