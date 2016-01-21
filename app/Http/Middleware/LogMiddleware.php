<?php
namespace App\Http\Middleware;

use App\Services\ServiceLog;
use Closure;

class LogMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Closure $next            
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        ServiceLog::requestLog($request, $response->content());
        return $response;
    }
}
