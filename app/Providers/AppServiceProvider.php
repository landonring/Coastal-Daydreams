<?php

namespace App\Providers;

use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $request = request();
        $host = $request->getHost();
        $vite = app(Vite::class);
        $forwardedProto = $request->headers->get('x-forwarded-proto');

        if ($request->isSecure() || $forwardedProto === 'https') {
            URL::forceScheme('https');
        }

        if (! in_array($host, ['127.0.0.1', 'localhost'], true)) {
            $vite->useHotFile(public_path('hot-disabled'));
            $vite->createAssetPathsUsing(static fn (string $path) => '/'.ltrim($path, '/'));
        }
    }
}
