<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuildAssets
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skontroluj build assets len pre web requesty
        if ($request->expectsJson() || $request->is('api/*')) {
            return $next($request);
        }

        $this->ensureBuildAssetsExist();

        return $next($request);
    }

    /**
     * Zabezpečí existenciu build assets
     */
    private function ensureBuildAssetsExist(): void
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!file_exists($manifestPath)) {
            $this->createMinimalBuildStructure();
        }
    }

    /**
     * Vytvorí minimálnu build štruktúru
     */
    private function createMinimalBuildStructure(): void
    {
        $buildDir = public_path('build');
        $assetsDir = public_path('build/assets');
        
        // Vytvor adresáre
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }
        
        if (!is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }

        // Skontroluj či existujú skutočné build súbory
        $cssFiles = glob($assetsDir . '/*.css');
        $jsFiles = glob($assetsDir . '/*.js');

        if (!empty($cssFiles) && !empty($jsFiles)) {
            // Vytvor manifest z existujúcich súborov
            $this->createManifestFromFiles($cssFiles, $jsFiles);
        } else {
            // Vytvor prázdne súbory a manifest
            $this->createEmptyBuildFiles();
        }
    }

    /**
     * Vytvorí manifest z existujúcich súborov
     */
    private function createManifestFromFiles(array $cssFiles, array $jsFiles): void
    {
        $manifest = [];
        
        if (!empty($cssFiles)) {
            $cssFile = basename($cssFiles[0]);
            $manifest['resources/css/app.css'] = [
                'file' => 'assets/' . $cssFile,
                'src' => 'resources/css/app.css'
            ];
        }
        
        if (!empty($jsFiles)) {
            $jsFile = basename($jsFiles[0]);
            $manifest['resources/js/app.js'] = [
                'file' => 'assets/' . $jsFile,
                'src' => 'resources/js/app.js'
            ];
        }

        file_put_contents(
            public_path('build/manifest.json'),
            json_encode($manifest, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Vytvorí prázdne build súbory
     */
    private function createEmptyBuildFiles(): void
    {
        $assetsDir = public_path('build/assets');
        
        // Vytvor prázdny CSS súbor
        $cssContent = "/* Prázdny CSS súbor - nahraj správne build súbory */\nbody { margin: 0; }";
        file_put_contents($assetsDir . '/app.css', $cssContent);
        
        // Vytvor prázdny JS súbor
        $jsContent = "/* Prázdny JS súbor - nahraj správne build súbory */\nconsole.log('Build súbory nie sú nahraté');";
        file_put_contents($assetsDir . '/app.js', $jsContent);
        
        // Vytvor manifest
        $manifest = [
            'resources/css/app.css' => [
                'file' => 'assets/app.css',
                'src' => 'resources/css/app.css'
            ],
            'resources/js/app.js' => [
                'file' => 'assets/app.js',
                'src' => 'resources/js/app.js'
            ]
        ];

        file_put_contents(
            public_path('build/manifest.json'),
            json_encode($manifest, JSON_PRETTY_PRINT)
        );
    }
} 