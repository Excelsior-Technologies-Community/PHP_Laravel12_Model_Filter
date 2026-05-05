@forelse($posts as $post)
<div class="post-card mb-3">

    <div class="d-flex justify-content-between">
        <h5>{{ $post->title }}</h5>

        <span class="badge {{ $post->is_published ? 'bg-success' : 'bg-secondary' }}">
            {{ $post->is_published ? 'Published' : 'Draft' }}
        </span>
    </div>

    <p class="post-content">{{ $post->content }}</p>

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <small>📅 {{ \Carbon\Carbon::parse($post->post_date)->format('d M Y') }}</small><br>
            <small>🕒 {{ $post->created_at->diffForHumans() }}</small>
        </div>

        <div>
            <!-- EDIT -->
            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-edit btn-sm">
                <i class="bi bi-pencil"></i>
            </a>

            <!-- DELETE -->
            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button class="btn btn-delete btn-sm">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>

    </div>

</div>
@empty
<p class="text-center text-muted">No posts found</p>
@endforelse