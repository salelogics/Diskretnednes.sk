<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('author')
            ->latest()
            ->paginate(10);
            
        return view('admin-page.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin-page.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'required|image|max:2048',
            'is_published' => 'boolean',
        ]);

        try {
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                
                if (!$imageFile->isValid()) {
                    throw new \Exception('Obrázok nie je platný');
                }

                // Vytvoríme adresár ak neexistuje
                Storage::disk('public')->makeDirectory('blog/images');
                
                $imageFileName = time() . '_' . Str::slug($imageFile->getClientOriginalName()) . '.' . $imageFile->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('blog/images', $imageFile, $imageFileName);
                $validated['image_path'] = 'storage/blog/images/' . $imageFileName;
            }

            $validated['slug'] = Str::slug($validated['title']);
            $validated['author_id'] = Auth::id();
            
            if ($request->has('is_published') && $request->is_published) {
                $validated['published_at'] = now();
            }

            $post = BlogPost::create($validated);

            return redirect()->route('admin.blog.index')
                ->with('success', 'Článok bol úspešne vytvorený.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Nastala chyba pri vytváraní článku: ' . $e->getMessage());
        }
    }

    public function edit(BlogPost $post)
    {
        return view('admin-page.blog.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
        ]);

        try {
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                
                if (!$imageFile->isValid()) {
                    throw new \Exception('Obrázok nie je platný');
                }

                // Vytvoríme adresár ak neexistuje
                Storage::disk('public')->makeDirectory('blog/images');
                
                // Zmažeme starý obrázok
                if ($post->image_path) {
                    $oldImagePath = str_replace('storage/', '', $post->image_path);
                    Storage::disk('public')->delete($oldImagePath);
                }
                
                $imageFileName = time() . '_' . Str::slug($imageFile->getClientOriginalName()) . '.' . $imageFile->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('blog/images', $imageFile, $imageFileName);
                $validated['image_path'] = 'storage/blog/images/' . $imageFileName;
            }

            $validated['slug'] = Str::slug($validated['title']);
            
            // Ak článok nebol publikovaný a teraz ho publikujeme
            if (!$post->is_published && $request->has('is_published') && $request->is_published) {
                $validated['published_at'] = now();
            }
            // Ak článok bol publikovaný a teraz ho odstraňujeme z publikovania
            elseif ($post->is_published && (!$request->has('is_published') || !$request->is_published)) {
                $validated['published_at'] = null;
            }

            $post->update($validated);

            return redirect()->route('admin.blog.index')
                ->with('success', 'Článok bol úspešne aktualizovaný.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Nastala chyba pri aktualizácii článku: ' . $e->getMessage());
        }
    }

    public function destroy(BlogPost $post)
    {
        try {
            if ($post->image_path) {
                $imagePath = str_replace('storage/', '', $post->image_path);
                Storage::disk('public')->delete($imagePath);
            }

            $post->delete();

            return redirect()->route('admin.blog.index')
                ->with('success', 'Článok bol úspešne odstránený.');
        } catch (\Exception $e) {
            return back()->with('error', 'Nastala chyba pri odstraňovaní článku: ' . $e->getMessage());
        }
    }
}
