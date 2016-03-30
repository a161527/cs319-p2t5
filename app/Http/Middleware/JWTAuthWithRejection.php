<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

/**
 * JWTAuth's Authenticate middleware, but rejects if user doesn't end
 * up set
 */
class JWTAuthWithRejection extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request);

        try {
            $this->auth->parseToken()->authenticate();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }

        if (is_null(Auth::user())) {
            throw new UnauthorizedHttpException('jwt-auth-rejection', "No user set up");
        }

        return $next($request);
    }
}

