<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // INDEX PAGE (list + filter)
    public function index(Request $request)
    {
        $query = Post::query();

        // ✅ TITLE FILTER
        if ($request->filled('title_filter')) {
            $query->where('title', 'like', '%' . $request->title_filter . '%');
        }

        // ✅ DATE FILTER (IMPORTANT)
        if ($request->filled('created_after_filter')) {
            $query->whereDate('post_date', $request->created_after_filter);
        }

        // ✅ STATUS FILTER
        if ($request->filled('published_filter')) {
            $query->where('is_published', $request->published_filter);
        }

        $posts = $query->latest()->get();

        return view('posts.index', compact('posts'));
    }

    // CREATE PAGE (form)
    public function create()
    {
        return view('posts.create');
    }

    // STORE DATA
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'post_date' => 'required|date',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => $request->is_published ?? 1,
            'post_date' => $request->post_date, // 👈 ADD THIS
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    // SHOW EDIT PAGE
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'post_date' => 'required',
        ]);

        $post = Post::findOrFail($id);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'post_date' => $request->post_date,
            'is_published' => $request->is_published
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully!');
    }

    // DELETE POST
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}