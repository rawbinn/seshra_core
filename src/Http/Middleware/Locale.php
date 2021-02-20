<?php

namespace Seshra\Core\Http\Middleware;

use Seshra\Core\Repositories\LocaleRepository;
use Closure;

/**
 * Class Locale
 * @package Seshra\Core\Http\Middleware
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Locale
{
    /**
     * @var LocaleRepository
     */
    protected $locale;

    /**
     * @param \Seshra\Core\Repositories\LocaleRepository $locale
     */
    public function __construct(LocaleRepository $locale)
    {
        $this->locale = $locale;
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
        $locale = request()->get('locale');

        if ($locale) {
            if ($this->locale->findOneByField('code', $locale)) {
                app()->setLocale($locale);

                session()->put('locale', $locale);
            }
        } else {
            if ($locale = session()->get('locale')) {
                app()->setLocale($locale);
            } else {
                app()->setLocale(config('app.fallback_locale'));
            }
        }

        unset($request['locale']);
        return $next($request);
    }
}
