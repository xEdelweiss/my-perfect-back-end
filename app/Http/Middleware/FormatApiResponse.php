<?php

namespace App\Http\Middleware;

use App\Components\ApiFormatter\ApiFormatter;
use Closure;

class FormatApiResponse
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
        $response = $next($request);

        if (!$request->wantsJson()) {
            return $response;
        }

        return (new ApiFormatter())->formatResponse($response);
    }
}
