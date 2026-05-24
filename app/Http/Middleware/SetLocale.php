<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    const SUPPORTED = ['es', 'en'];
    const DEFAULT   = 'es';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (!$locale) {
            $locale = $this->detectFromBrowser($request);
            session(['locale' => $locale]);
        }

        app()->setLocale($locale);

        return $next($request);
    }

    private function detectFromBrowser(Request $request): string
    {
        $accept = $request->header('Accept-Language', '');

        foreach (explode(',', $accept) as $part) {
            $lang = strtolower(substr(trim($part), 0, 2));
            if (in_array($lang, self::SUPPORTED)) {
                return $lang;
            }
        }

        return self::DEFAULT;
    }
}
