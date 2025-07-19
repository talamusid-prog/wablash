<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for API key in header
        $apiKey = $request->header('X-API-Key');
        
        if ($apiKey) {
            // Validate API key (you can store this in config or database)
            if ($this->validateApiKey($apiKey)) {
                return $next($request);
            }
        }

        // Check for Bearer token
        $token = $request->bearerToken();
        
        if ($token) {
            // Try to authenticate with Sanctum
            if (Auth::guard('sanctum')->check()) {
                return $next($request);
            }
        }

        // Check for basic auth
        if ($request->getUser() && $request->getPassword()) {
            $credentials = [
                'email' => $request->getUser(),
                'password' => $request->getPassword()
            ];

            if (Auth::attempt($credentials)) {
                return $next($request);
            }
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized API access attempt', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access. Please provide valid authentication credentials.',
            'error_code' => 'UNAUTHORIZED'
        ], 401);
    }

    /**
     * Validate API key
     */
    private function validateApiKey($apiKey)
    {
        // Get valid API keys from config
        $validApiKeys = config('api.valid_api_keys', []);
        
        // Filter out empty values
        $validApiKeys = array_filter($validApiKeys);
        
        // If no API keys configured, allow access for development
        if (empty($validApiKeys)) {
            Log::warning('No API keys configured, allowing access for development');
            return true;
        }
        
        return in_array($apiKey, $validApiKeys);
    }
} 