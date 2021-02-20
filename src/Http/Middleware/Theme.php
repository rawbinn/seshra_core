<?php

namespace Seshra\Core\Http\Middleware;

use Closure;

/**
 * Class Theme
 * @package Seshra\Core\Http\Middleware
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Theme
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
        $themes = app('themes');
        $themes->set(config('themes.default'));
        return $next($request);
    }
}