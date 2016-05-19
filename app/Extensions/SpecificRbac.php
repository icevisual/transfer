<?php
namespace App\Http\Middleware;

use Closure;

class SpecificRbac extends SpecificRbacConst
{

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth            
     * @return void
     */
    public function __construct()
    {}

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param \Closure $next            
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
