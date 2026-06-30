<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PrivateFileController extends Controller
{
    /**
     * Serve user's profile photo from private storage with basic authorization.
     */
    public function profilePhoto(Request $request, string $path): BinaryFileResponse
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        // Allow access if the requested path matches current user's photo, or the user is admin
        $normalizedPath = ltrim($path, '/');
        $isOwner = $user->photo && $normalizedPath === $user->photo;
        $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : false;

        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        // Try private first, then legacy public path for backward compatibility
        $absolutePath = storage_path('app/private/' . $normalizedPath);
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            $legacyPublicPath = storage_path('app/public/' . $normalizedPath);
            if (file_exists($legacyPublicPath) && is_file($legacyPublicPath)) {
                $absolutePath = $legacyPublicPath;
            } else {
                abort(404);
            }
        }

        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}


