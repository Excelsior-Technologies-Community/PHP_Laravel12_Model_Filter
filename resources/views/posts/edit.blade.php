<!DOCTYPE html>
<html>

<head>
    <title>Edit Post</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #020617, #0f172a);
            color: #e2e8f0;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 30px;
            width: 420px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        input,
        textarea,
        select {
            background: #020617 !important;
            border: 1px solid #334155 !important;
            color: white !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #94a3b8 !important;
        }

        .btn-update {
            background: #22c55e;
            border: none;
        }

        .btn-update:hover {
            background: #16a34a;
        }

        .btn-back {
            background: #475569;
            border: none;
        }

        .btn-back:hover {
            background: #334155;
        }

        /* Calendar */
        .flatpickr-calendar {
            background: #020617;
            color: white;
        }
    </style>
</head>

<body>

    <div class="d-flex justify-content-center align-items-center vh-100">

        <div class="form-card">

            <h4 class="text-center mb-4">
                <i class="bi bi-pencil"></i> Edit Post
            </h4>

            <form method="POST" action="{{ route('posts.update', $post->id) }}">
                @csrf
                @method('PUT')

                <!-- TITLE -->
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" value="{{ $post->title }}" placeholder="Title">
                </div>

                <!-- CONTENT -->
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3"
                        placeholder="Content">{{ $post->content }}</textarea>
                </div>

                <!-- DATE -->
                <div class="mb-3">
                    <input type="text" id="post_date" name="post_date" class="form-control"
                        value="{{ $post->post_date }}">
                </div>

                <!-- STATUS -->
                <div class="mb-3">
                    <select name="is_published" class="form-select">
                        <option value="1" {{ $post->is_published ? 'selected' : '' }}>Published</option>
                        <option value="0" {{ !$post->is_published ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <!-- UPDATE -->
                <button type="submit" class="btn btn-update w-100 mb-2">
                    🔄 Update Post
                </button>
            </form>

            <!-- BACK -->
            <a href="{{ route('posts.index') }}" class="btn btn-back w-100">
                ⬅ Back
            </a>

        </div>

    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr("#post_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            defaultDate: "{{ $post->post_date }}"
        });
    </script>

</body>

</html>