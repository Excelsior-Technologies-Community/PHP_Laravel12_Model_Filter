<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ Flatpickr Calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #020617, #0f172a);
            color: #e2e8f0;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 30px;
            width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        input, textarea, select {
            background: #020617 !important;
            border: 1px solid #334155 !important;
            color: white !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #94a3b8 !important;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99,102,241,0.3);
        }

        .btn-save {
            background: #22c55e;
            border: none;
        }

        .btn-save:hover {
            background: #16a34a;
        }

        .btn-back {
            background: #475569;
            border: none;
        }

        .btn-back:hover {
            background: #334155;
        }

        /* ✅ Calendar Dark Theme */
        .flatpickr-calendar {
            background: #020617;
            color: white;
            border: 1px solid #334155;
        }

        .flatpickr-day {
            color: white;
        }

        .flatpickr-day.selected {
            background: #22c55e;
        }

        .flatpickr-day:hover {
            background: #6366f1;
        }

        .flatpickr-months .flatpickr-month {
            color: white;
        }

        .flatpickr-current-month select {
            background: #020617;
            color: white;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center vh-100">

    <div class="form-card">

        <h4 class="text-center mb-4">
            <i class="bi bi-pencil-square"></i> Create New Post
        </h4>

        <form method="POST" action="{{ route('posts.store') }}">
            @csrf

            <!-- Title -->
            <div class="mb-3">
                <input type="text" name="title" class="form-control" placeholder="Enter Title">
            </div>

            <!-- Content -->
            <div class="mb-3">
                <textarea name="content" class="form-control" rows="3" placeholder="Enter Content"></textarea>
            </div>

            <!-- ✅ Calendar Input -->
            <div class="mb-3">
                <input type="text" id="post_date" name="post_date" class="form-control" placeholder="Select Date">
            </div>

            <!-- Status -->
            <div class="mb-3">
                <select name="is_published" class="form-select">
                    <option value="1">Published</option>
                    <option value="0">Draft</option>
                </select>
            </div>

            <!-- Save -->
            <button type="submit" class="btn btn-save w-100 mb-2">
                💾 Save Post
            </button>
        </form>

        <!-- Back -->
        <a href="{{ route('posts.index') }}" class="btn btn-back w-100">
            ⬅ Back
        </a>

    </div>

</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- ✅ Calendar Script -->
<script>
    flatpickr("#post_date", {
        dateFormat: "Y-m-d",      // DB format
        altInput: true,
        altFormat: "d F Y",       // Example: 30 March 2026
        allowInput: false,
        clickOpens: true,
        monthSelectorType: "dropdown", // ✅ month dropdown
        yearSelectorType: "dropdown"   // ✅ year dropdown
    });
</script>

</body>
</html>