<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GzipMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Skip if already compressed or not HTML/CSS/JS
        if (
            $response->headers->get('Content-Encoding') ||
            !$this->shouldCompress($response) ||
            !$this->supportsGzip($request)
        ) {
            return $response;
        }
        
        $content = $response->getContent();
        
        // Only compress if content is large enough
        if (strlen($content) < 1024) {
            return $response;
        }
        
        // Compress content
        $compressed = gzencode($content, 9);
        
        if ($compressed === false) {
            return $response;
        }
        
        // Set compressed content and headers
        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Content-Length', strlen($compressed));
        $response->headers->set('Vary', 'Accept-Encoding');
        
        return $response;
    }
    
    private function shouldCompress($response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        
        $compressibleTypes = [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'text/plain',
            'image/svg+xml'
        ];
        
        foreach ($compressibleTypes as $type) {
            if (str_contains($contentType, $type)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function supportsGzip(Request $request): bool
    {
        $acceptEncoding = $request->header('Accept-Encoding', '');
        return str_contains($acceptEncoding, 'gzip');
    }
} 