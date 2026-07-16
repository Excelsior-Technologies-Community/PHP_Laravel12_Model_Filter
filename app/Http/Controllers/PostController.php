<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\FilterPreset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // Sort
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'title_asc'  => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $posts = $query->paginate(10)->withQueryString();

        $totalPosts     = Post::count();
        $publishedPosts = Post::where('is_published', 1)->count();
        $draftPosts     = Post::where('is_published', 0)->count();
        $todayPosts     = Post::whereDate('created_at', today())->count();
        $presets        = FilterPreset::orderBy('name')->get();

        return view('posts.index', compact(
            'posts', 'totalPosts', 'publishedPosts',
            'draftPosts', 'todayPosts', 'presets'
        ));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required',
            'content'   => 'required',
            'post_date' => 'required|date',
        ]);

        Post::create([
            'title'        => $request->title,
            'content'      => $request->content,
            'is_published' => $request->is_published ?? 1,
            'post_date'    => $request->post_date,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'     => 'required',
            'content'   => 'required',
            'post_date' => 'required',
        ]);

        Post::findOrFail($id)->update([
            'title'        => $request->title,
            'content'      => $request->content,
            'post_date'    => $request->post_date,
            'is_published' => $request->is_published,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['is_published' => !$post->is_published]);
        return back()->with('success', 'Post status updated!');
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

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'title_asc'  => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'oldest'     => $query->orderBy('created_at', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $posts = $query->paginate(10);

        return view('posts.partials.post_list', compact('posts'))->render();
    }

    // --- Filter Presets ---

    public function savePreset(Request $request)
    {
        $request->validate(['preset_name' => 'required|string|max:100']);

        FilterPreset::create([
            'name'    => $request->preset_name,
            'filters' => [
                'title_filter'          => $request->title_filter,
                'created_after_filter'  => $request->created_after_filter,
                'published_filter'      => $request->published_filter,
                'sort'                  => $request->sort,
            ],
        ]);

        return redirect()->route('posts.index')->with('success', 'Preset saved!');
    }

    public function deletePreset($id)
    {
        FilterPreset::findOrFail($id)->delete();
        return redirect()->route('posts.index')->with('success', 'Preset deleted!');
    }
}
