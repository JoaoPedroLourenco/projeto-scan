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
        RateLimiter::for('user', function (Request $request) {
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
                ->response(function (Request $request, array $headers) {
                    $retryAfter = $headers['Retry-After'] ?? $headers['retry-After'] ?? 60;

                    return response()->json([
                        'msg' => 'Você excedeu o limite de requisições. Por favor, aguarde antes de tentar novamente.',
                        'retry_after_seconds' => (int) $retryAfter,
                    ], 429);
                });
        });

        RateLimiter::for('authenticate', function (Request $request) {
            $email = (string) $request->input('email');
            $ip = $request->ip();

            return [
                Limit::perMinute(10)->by($ip),

                Limit::perMinute(5)->by($email ? $email . '|' . $ip : $ip),
            ];
        });
    }
}