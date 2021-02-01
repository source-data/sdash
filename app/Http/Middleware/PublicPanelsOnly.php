<?php

namespace App\Http\Middleware;

use Closure;
use \App\Models\Panel;

class PublicPanelsOnly
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
        if (!isset($request->route('panel')->made_public_at)) abort(401, "Access denied");
        return $next($request);
    }
}
