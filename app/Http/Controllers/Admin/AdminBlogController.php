<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    public function index()
    {
        $articles = BlogPost::orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'total_articles' => BlogPost::count(),
            'published_articles' => BlogPost::where('is_published', true)->count(),
            'draft_articles' => BlogPost::where('is_published', false)->count(),
        ];

        return view('admin.blog.index', compact('articles', 'stats'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|json',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->filled('excerpt') ? $request->excerpt : null,
            'is_published' => $request->status === 'published',
            'author_id' => auth()->id(),
            'published_at' => $request->status === 'published' ? now() : null,
        ];

        if ($request->hasFile('featured_image')) {
            try {
                $file = $request->file('featured_image');
                
                \Log::info('Featured image upload started', [
                    'has_file' => $request->hasFile('featured_image'),
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'file_extension' => $file->getClientOriginalExtension(),
                    'is_valid' => $file->isValid(),
                    'error_code' => $file->getError(),
                    'temp_path' => $file->getPathname()
                ]);
                
                // Overenie, že súbor je validný
                if (!$file->isValid()) {
                    \Log::error('Featured image file is not valid', ['error' => $file->getError()]);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'Nahraný súbor je poškodený.']);
                }
                
                // Overenie že blog adresár existuje na disku public
                $blogDir = 'blog/images';
                \Storage::disk('public')->makeDirectory($blogDir);
                
                \Log::info('Attempting to store featured image', [
                    'blog_dir' => $blogDir,
                    'blog_exists' => file_exists($blogDir),
                    'blog_writable' => is_writable($blogDir)
                ]);
                
                // Vytvorenie jedinečného názvu súboru
                $extension = $file->getClientOriginalExtension();
                if (empty($extension)) {
                    // Fallback na základe MIME typu
                    $mimeType = $file->getClientMimeType();
                    $extension = match($mimeType) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                        'image/webp' => 'webp',
                        'image/svg+xml' => 'svg',
                        default => 'jpg'
                    };
                }
                
                $filename = time() . '_' . uniqid() . '.' . $extension;
                // Uloženie do storage/app/public/blog/images
                \Storage::disk('public')->putFileAs($blogDir, $file, $filename);
                $path = 'storage/blog/images/' . $filename;
                \Log::info('Featured image stored successfully', [
                    'filename' => $filename,
                    'path' => $path
                ]);
                
                \Log::info('Featured image store result', [
                    'path' => $path,
                    'success' => !empty($path)
                ]);
                
                if (empty($path)) {
                    \Log::error('Featured image store returned empty path');
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'Nepodarilo sa uložiť obrázok - prázdna cesta.']);
                }
                
                $data['image_path'] = $path;
                
            } catch (\Exception $e) {
                \Log::error('Blog image upload error in store', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'Chyba pri nahrávaní obrázka: ' . $e->getMessage()]);
            }
        }

        try {
            BlogPost::create($data);
            return redirect()->route('admin.clanky.index')->with('success', 'Článok bol úspešne vytvorený.');
        } catch (\Exception $e) {
            \Log::error('Blog post creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Chyba pri vytváraní článku: ' . $e->getMessage()]);
        }
    }

    public function show(BlogPost $article)
    {
        return view('admin.blog.show', compact('article'));
    }

    public function edit(BlogPost $article)
    {
        return view('admin.blog.edit', compact('article'));
    }

    public function update(Request $request, BlogPost $article)
    {
        try {
            \Log::info('Blog update started', [
                'article_id' => $article->id,
                'request_data' => $request->except(['featured_image']),
                'has_image' => $request->hasFile('featured_image')
            ]);

            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|json',
                'excerpt' => 'nullable|string|max:500',
                'status' => 'required|in:draft,published',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
            ]);

            $data = [
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                'excerpt' => $request->filled('excerpt') ? $request->excerpt : null,
                'is_published' => $request->status === 'published',
            ];

            if ($request->status === 'published' && !$article->published_at) {
                $data['published_at'] = now();
            }

            if ($request->hasFile('featured_image')) {
                try {
                    if ($article->image_path) {
                        $oldPath = public_path($article->image_path);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    
                    $file = $request->file('featured_image');
                    $extension = $file->getClientOriginalExtension();
                    if (empty($extension)) {
                        $mimeType = $file->getClientMimeType();
                        $extension = match($mimeType) {
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'image/webp' => 'webp',
                            'image/svg+xml' => 'svg',
                            default => 'jpg'
                        };
                    }
                    
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                $blogDir = 'blog/images';
                \Storage::disk('public')->makeDirectory($blogDir);
                \Storage::disk('public')->putFileAs($blogDir, $file, $filename);
                $data['image_path'] = 'storage/blog/images/' . $filename;
                \Log::info('Image uploaded successfully', ['filename' => $filename]);
                } catch (\Exception $e) {
                    \Log::error('Image upload failed', ['error' => $e->getMessage()]);
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'Chyba pri nahrávaní obrázku: ' . $e->getMessage()]);
                }
            }

            $article->update($data);

            \Log::info('Blog update completed successfully', ['article_id' => $article->id]);

            return redirect()->route('admin.clanky.index')->with('success', 'Článok bol úspešne aktualizovaný.');
            
        } catch (\Exception $e) {
            \Log::error('Blog update failed', [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Chyba pri aktualizácii článku: ' . $e->getMessage()]);
        }
    }

    public function destroy(BlogPost $article)
    {
        if ($article->image_path) {
            // očistí cestu pre disk 'public' (odstráni 'storage/' prefix)
            $relativePath = str_starts_with($article->image_path, 'storage/')
                ? substr($article->image_path, strlen('storage/'))
                : $article->image_path;
            Storage::disk('public')->delete($relativePath);
        }
        
        $article->delete();
        
        return redirect()->route('admin.clanky.index')->with('success', 'Článok bol úspešne vymazaný.');
    }

    // API endpoints pre Editor.js
    public function uploadImage(Request $request)
    {
        try {
            \Log::info('Upload request started', [
                'has_file_image' => $request->hasFile('image'),
                'all_files' => array_keys($request->allFiles()),
                'all_input' => $request->all()
            ]);

            // Editor.js Image tool môže posielať súbor pod rôznymi názvami
            $fileField = null;
            $file = null;
            
            if ($request->hasFile('image')) {
                $fileField = 'image';
                $file = $request->file('image');
            } elseif ($request->hasFile('file')) {
                $fileField = 'file';
                $file = $request->file('file');
            } else {
                // Skúsme nájsť akýkoľvek súbor
                $files = $request->allFiles();
                if (!empty($files)) {
                    $fileField = array_keys($files)[0];
                    $file = $files[$fileField];
                }
            }

            if (!$file) {
                \Log::error('No file in request', [
                    'all_files' => array_keys($request->allFiles()),
                    'all_input' => $request->all()
                ]);
                return response()->json([
                    'success' => 0,
                    'message' => 'Súbor nebol nahraný'
                ], 400);
            }

            // Validácia súboru
            $validator = \Validator::make([$fileField => $file], [
                $fileField => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::error('File validation failed', ['errors' => $validator->errors()->all()]);
                return response()->json([
                    'success' => 0,
                    'message' => 'Neplatný súbor: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }
            
            \Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError()
            ]);
            
            if (!$file->isValid()) {
                \Log::error('File is not valid', ['error' => $file->getError()]);
                return response()->json([
                    'success' => 0,
                    'message' => 'Súbor je poškodený'
                ], 400);
            }

            // Získanie extension s fallback
            $extension = $file->getClientOriginalExtension();
            if (empty($extension)) {
                $extension = 'jpg'; // fallback
            }

            // Vytvorenie jedinečného názvu súboru
            $filename = time() . '_' . uniqid() . '.' . $extension;
            
            \Log::info('Attempting to store file', [
                'filename' => $filename,
                'directory' => 'blog/images',
                'disk' => 'public'
            ]);
            
            // Overenie že adresár existuje
            $directory = 'blog/images';
            \Storage::disk('public')->makeDirectory($directory);
            \Storage::disk('public')->putFileAs($directory, $file, $filename);
            $path = 'storage/blog/images/' . $filename;
            
            \Log::info('File storage result', [
                'path' => $path,
                'success' => !empty($path)
            ]);
            
            if (empty($path)) {
                \Log::error('Failed to store file - empty path returned');
                return response()->json([
                    'success' => 0,
                    'message' => 'Nepodarilo sa uložiť súbor - prázdna cesta'
                ], 500);
            }
            
            $url = asset($path);
            
            \Log::info('Upload successful', [
                'path' => $path,
                'url' => $url
            ]);
            
            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $url
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Blog image upload error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => 0,
                'message' => 'Chyba pri nahrávaní obrázka: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadImageByUrl(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|url'
            ]);

            $url = $request->url;
            
            // Základná kontrola či URL obsahuje obrázok
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $urlParts = parse_url($url);
            $pathInfo = pathinfo($urlParts['path'] ?? '');
            $extension = strtolower($pathInfo['extension'] ?? '');
            
            if (!in_array($extension, $imageExtensions)) {
                return response()->json([
                    'success' => 0,
                    'message' => 'URL neobsahuje platný obrázok'
                ], 400);
            }

            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $url
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Neplatná URL: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Chyba pri spracovaní URL'
            ], 500);
        }
    }

    public function fetchUrl(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|url'
            ]);

            $url = $request->url;
            
            // Základné informácie o URL (zjednodušené)
            $parsedUrl = parse_url($url);
            $title = $parsedUrl['host'] ?? 'Link';
            $description = '';
            $image = '';

            return response()->json([
                'success' => 1,
                'link' => $url,
                'meta' => [
                    'title' => $title,
                    'description' => $description,
                    'image' => [
                        'url' => $image
                    ]
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Neplatná URL: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Nepodarilo sa načítať informácie o URL'
            ], 500);
        }
    }
} 