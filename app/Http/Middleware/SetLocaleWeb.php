<?php

namespace App\Http\Middleware;

use Closure;

class SetLocaleWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->get('lang', session('locale', config('app.locale')));
        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
