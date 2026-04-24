<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $styleSources = ["'self'", "'unsafe-inline'"];
        $scriptSources = ["'self'", "'unsafe-inline'", "'unsafe-eval'"];
        $connectSources = ["'self'"];

        foreach ($this->viteDevSources() as $source) {
            $styleSources[] = $source['http'];
            $scriptSources[] = $source['http'];
            $connectSources[] = $source['http'];
            $connectSources[] = $source['ws'];
        }

        $csp = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            'style-src '.implode(' ', array_unique($styleSources)),
            'script-src '.implode(' ', array_unique($scriptSources)),
            'connect-src '.implode(' ', array_unique($connectSources)),
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    protected function viteDevSources(): array
    {
        $defaults = [
            ['http' => 'http://127.0.0.1:5173', 'ws' => 'ws://127.0.0.1:5173'],
            ['http' => 'http://localhost:5173', 'ws' => 'ws://localhost:5173'],
        ];

        $hotFile = public_path('hot');

        if (! file_exists($hotFile)) {
            return $defaults;
        }

        $hotUrl = trim((string) file_get_contents($hotFile));

        if (! Str::startsWith($hotUrl, ['http://', 'https://'])) {
            return $defaults;
        }

        return array_merge($defaults, [[
            'http' => $hotUrl,
            'ws' => preg_replace('/^http/i', 'ws', $hotUrl),
        ]]);
    }
}
