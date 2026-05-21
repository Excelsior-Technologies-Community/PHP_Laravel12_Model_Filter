<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->filled('title_filter')) {
            $query->where('title', 'like', '%' . $request->title_filter . '%');
        }

        if ($request->filled('created_after_filter')) {
            $query->whereDate('post_date', $request->created_after_filter);
        }

        if ($request->filled('published_filter')) {
            $query->where('is_published', $request->published_filter);
        }

        $posts = $query->orderBy('id', 'asc')->paginate(3);

        $totalPosts = Post::count();
        $publishedPosts = Post::where('is_published', 1)->count();
        $draftPosts = Post::where('is_published', 0)->count();
        $todayPosts = Post::whereDate('created_at', today())->count();

        return view('posts.index', compact(
            'posts',
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'todayPosts'
        ));
    }   

    public function create()
    {
        return view('posts.create');
    }

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
            'post_date' => $request->post_date,
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    public function search(Request $request)
    {
        $query = Post::query();

        if ($request->title) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_published', $request->status);
        }

        if ($request->date) {
            $query->whereDate('post_date', $request->date);
        }

        $posts = $query->latest()->paginate(5);

        return view('posts.partials.post_list', compact('posts'))->render();
    }

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

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);

        $post->is_published = !$post->is_published;
        $post->save();

        return back()->with(
            'success',
            'Post status updated successfully!'
        );
    }
}