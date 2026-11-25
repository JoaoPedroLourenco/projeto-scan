<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        $customResponse = function (Request $request, array $headers) {
            $retryAfter = $headers['Retry-After'] ?? $headers['retry-After'] ?? $headers['retry-after'] ?? 60;

            return response()->json([
                'ok'                  => false,
                'msg'                 => 'Você excedeu o limite de requisições. Por favor, aguarde antes de tentar novamente.',
                'retry_after_seconds' => (int) $retryAfter,
            ], 429, $headers);
        };

        RateLimiter::for('user', function (Request $request) use ($customResponse) {
            $user = $request->user();

            // Define limite de requisições por minuto por cargo
            $maxAttempts = match ($user?->role) {
                'admin'    => 120, 
                'operador' => 60,  
                'cliente'   => 30,  
                default    => 10,  
            };

            return Limit::perMinute($maxAttempts)
                ->by($user?->id ?: $request->ip())
                ->response($customResponse);
        });

        RateLimiter::for('authenticate', function (Request $request) use ($customResponse) {
            $email = (string) $request->input('email');
            $ip = $request->ip();

            return [
                Limit::perMinute(10)
                       ->by($ip)
                       ->response($customResponse),

                Limit::perMinute(5)
                       ->by($email ? $email . '|' . $ip : $ip)
                       ->response($customResponse),
            ];
        });
    }
}