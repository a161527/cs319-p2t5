<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Token;


class JWTDownloadURLToken
{

    private $auth;
    private $manager;

    public function __construct(Manager $manager, Auth $auth) {
        $this->auth = $auth;
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->has('token')) {
            throw new BadRequestHttpException('Request does not have token');
        }
        $token = new Token($request->input('token'));
        $id = $this->manager->decode($token)->get('sub');

        if (!$this->auth->byId($id)) {
            throw UnauthorizedHttpException("Token not valid");
        }

        return $next($request);
    }
}
