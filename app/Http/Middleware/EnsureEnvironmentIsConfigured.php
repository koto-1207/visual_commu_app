<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureEnvironmentIsConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 本番環境でのみチェック
        if (app()->environment('production')) {
            $this->checkRequiredEnvironmentVariables();
        }

        return $next($request);
    }

    /**
     * 必須の環境変数をチェック
     */
    protected function checkRequiredEnvironmentVariables(): void
    {
        $required = [
            'APP_KEY',
            'DB_CONNECTION',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
        ];

        $missing = [];

        foreach ($required as $var) {
            if (empty(env($var))) {
                $missing[] = $var;
            }
        }

        if (!empty($missing)) {
            Log::error('Missing required environment variables', [
                'missing' => $missing,
            ]);
        }
    }
}
