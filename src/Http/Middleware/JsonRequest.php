<?php

namespace Mas\Http\Middleware;

use Closure;

class JsonRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( ! $request->isJson()) {
            return response()->json(["error" => "wrongFormat", "errorMessage" => "Request format must be a json object"]);
        }

        return $next($request);
    }
}
