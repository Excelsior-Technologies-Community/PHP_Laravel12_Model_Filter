<!DOCTYPE html>
<html>

<head>
    <title>Posts Dashboard</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert -->
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

        .btn-custom {
            background: #6366f1;
        }

        .btn-edit {
            background: #0ea5e9;
        }

        .btn-delete {
            background: #ef4444;
        }

        #loader {
            text-align: center;
            padding: 20px;
            display: none;
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

        <!-- ✅ SUCCESS TOAST -->
        @if(session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
        @endif

        <!-- 🔍 LIVE FILTER -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <input type="text" id="title" class="form-control" placeholder="🔍 Search title">
            </div>

            <div class="col-md-3">
                <input type="date" id="date" class="form-control">
            </div>

            <div class="col-md-3">
                <select id="status" class="form-select">
                    <option value="">Status</option>
                    <option value="1">Published</option>
                    <option value="0">Draft</option>
                </select>
            </div>

        </div>

        <!-- 🔄 LOADER -->
        <div id="loader">Loading...</div>

        <!-- 📦 POSTS LIST -->
        <div id="post-list">
            @include('posts.partials.post_list')
        </div>

    </div>
</div>

<!-- DELETE CONFIRM -->
<script>
function bindDelete() {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
}
bindDelete();
</script>

<!-- 🔥 LIVE SEARCH SCRIPT -->
<script>
const title = document.getElementById('title');
const status = document.getElementById('status');
const date = document.getElementById('date');
const loader = document.getElementById('loader');
const postList = document.getElementById('post-list');

let timer;

function fetchPosts() {

    loader.style.display = 'block';

    let url = `/posts/search?title=${title.value}&status=${status.value}&date=${date.value}`;

    fetch(url)
        .then(res => res.text())
        .then(data => {
            postList.innerHTML = data;
            loader.style.display = 'none';
            bindDelete(); // rebind delete
        });
}

// 🔥 debounce typing
title.addEventListener('keyup', () => {
    clearTimeout(timer);
    timer = setTimeout(fetchPosts, 300);
});

status.addEventListener('change', fetchPosts);
date.addEventListener('change', fetchPosts);

</script>

</body>
</html>