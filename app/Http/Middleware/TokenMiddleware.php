<?php

namespace App\Http\Middleware;

use Closure;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data['uid'] = $request->input('uid');
        $data['token'] = $request->input('token');
        
        if(\App\Services\User\UserService::checkAuthToken($data)){
            return $next($request);
        }
        throw new \App\Exceptions\ServiceException('授权失败',600);
        
    }
}
