<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageVisit;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Solo rastrear visitas para requests GET que devuelvan HTML
        if ($request->method() === 'GET' && 
            $response->isSuccessful() && 
            $this->isHtmlResponse($response)) {
            
            $this->recordPageVisit($request);
        }
        
        return $response;
    }
    
    /**
     * Verificar si la respuesta es HTML
     */
    private function isHtmlResponse($response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'text/html') || 
               str_contains($contentType, 'text/plain') ||
               empty($contentType);
    }
    
    /**
     * Registrar la visita a la página
     */
    private function recordPageVisit(Request $request): void
    {
        try {
            $pageName = $this->getPageName($request);
            $pageUrl = $request->fullUrl();
            
            // Evitar rastrear ciertas rutas
            if ($this->shouldSkipTracking($request)) {
                return;
            }
            
            PageVisit::incrementVisit($pageName, $pageUrl);
            
        } catch (\Exception $e) {
            // Log el error pero no interrumpir la aplicación
            \Log::error('Error tracking page visit: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener el nombre de la página basado en la ruta
     */
    private function getPageName(Request $request): string
    {
        $routeName = $request->route()?->getName();
        
        if ($routeName) {
            return $routeName;
        }
        
        // Si no hay nombre de ruta, usar el path
        $path = trim($request->getPathInfo(), '/');
        
        if (empty($path)) {
            return 'home';
        }
        
        // Convertir path a nombre amigable
        return str_replace(['/', '-', '_'], ['.', ' ', ' '], $path);
    }
    
    /**
     * Determinar si debemos omitir el rastreo para esta ruta
     */
    private function shouldSkipTracking(Request $request): bool
    {
        $skipRoutes = [
            'api/*',
            'admin/*',
            'livewire/*',
            '_ignition/*',
            'telescope/*',
            'horizon/*',
        ];
        
        $path = $request->getPathInfo();
        
        foreach ($skipRoutes as $pattern) {
            if (fnmatch($pattern, trim($path, '/') . '/*') || 
                fnmatch($pattern, trim($path, '/'))) {
                return true;
            }
        }
        
        // Skip AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return true;
        }
        
        return false;
    }
}