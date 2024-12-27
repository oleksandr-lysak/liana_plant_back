<?php

namespace App\Http\Middleware;

use Closure;

class CompressResponse
{
    public function handle($request, Closure $next)
    {
        // get response from next middleware
        $response = $next($request);

        // if client support zip then use GZIP
        if (str_contains($request->header('Accept-Encoding'), 'gzip')) {
            $content = gzencode($response->getContent());
            $response->setContent($content);
            $response->headers->set('Content-Encoding', 'gzip');
        }

        return $response;
    }
}
