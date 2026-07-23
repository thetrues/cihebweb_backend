<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $allowedHeaders = [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'Accept',
        'Origin',
        'X-CSRF-TOKEN'
    ];
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $allowedOrigins = [
            'https://cqi.qi-mis.org', 
            'http://cqi.qi-mis.org', 
            'http://localhost:3000',
            'https://qi-mis.org:3030',  // Add your frontend URL
            'http://qi-mis.org:3030',
            'http://localhost:8080',
            'https://cqi-api.qi-mis.org',
            'http://cqi-api.qi-mis.org'
        ];
        
        $isAllowedOrigin = $origin && in_array($origin, $allowedOrigins);

        
        
        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 205);
        } else {
            $response = $next($request);
        }
        
        if ($isAllowedOrigin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders));
            $response->headers->set('Access-Control-Max-Age', '3600');
            $response->headers->set('Cross-Origin-Resource-Policy', 'cross-origin');
        }
        
        return $response;
    }
}
