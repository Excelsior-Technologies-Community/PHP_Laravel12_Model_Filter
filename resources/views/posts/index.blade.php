<!DOCTYPE html>
<html>

<head>
    <title>Posts Dashboard</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #e2e8f0;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .post-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            padding: 15px;
            transition: 0.3s;
        }

        .post-card:hover {
            transform: scale(1.02);
            background: rgba(255, 255, 255, 0.08);
        }

        input,
        select {
            background: #0f172a !important;
            border: 1px solid #334155 !important;
            color: white !important;
        }

        input::placeholder {
            color: #94a3b8 !important;
        }

        .post-content {
            color: #e2e8f0;
        }

        .btn-custom {
            background: #6366f1;
            border: none;
        }

        .btn-custom:hover {
            background: #4f46e5;
        }

        .btn-edit {
            background: #0ea5e9;
            border: none;
        }

        .btn-edit:hover {
            background: #0284c7;
        }

        .btn-delete {
            background: #ef4444;
            border: none;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .badge {
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="container mt-5">

        <div class="main-card">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-speedometer2"></i> Posts Dashboard</h3>

                <a href="{{ route('posts.create') }}" class="btn btn-custom">
                    <i class="bi bi-plus-circle"></i> New Post
                </a>
            </div>

            <!-- SUCCESS -->
            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#22c55e'
                    });
                </script>
            @endif

            <!-- FILTER -->
            <form method="GET" action="{{ route('posts.index') }}" class="row g-3 mb-4">

                <div class="col-md-4">
                    <input type="text" name="title_filter" class="form-control" placeholder="🔍 Search title"
                        value="{{ request('title_filter') }}">
                </div>

                <div class="col-md-3">
                    <input type="date" name="created_after_filter" class="form-control"
                        value="{{ request('created_after_filter') }}">
                </div>

                <div class="col-md-3">
                    <select name="published_filter" class="form-select">
                        <option value="">Status</option>
                        <option value="1" {{ request('published_filter') == '1' ? 'selected' : '' }}>Published</option>
                        <option value="0" {{ request('published_filter') == '0' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>

            </form>

            <!-- POSTS -->
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

                        <!-- ✅ ACTION BUTTONS -->
                        <div>
                            <!-- EDIT -->
                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-edit btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <!-- DELETE -->
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;"
                                class="delete-form">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-delete btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
            @empty
                <p class="text-center text-muted">No posts found</p>
            @endforelse

        </div>
    </div>

    <!-- ✅ DELETE CONFIRMATION -->
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

</body>

</html>