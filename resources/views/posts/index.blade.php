<!DOCTYPE html>
<html>

<head>
    <title>Posts Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
        }

        .main-card {
            background: rgba(255, 255, 255, .05);
            padding: 25px;
            border-radius: 20px;
        }

        .post-card {
            background: rgba(255, 255, 255, .05);
            padding: 15px;
            border-radius: 15px;
        }

        .btn-custom {
            background: #6366f1;
            color: white;
        }

        #loader {
            display: none;
            text-align: center;
            padding: 20px;
        }

    </style>
</head>

<body>

    <div class="container mt-5">

        <div class="main-card">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-speedometer2"></i> Posts Dashboard</h3>

                <a href="{{ route('posts.create') }}" class="btn btn-custom">
                    <i class="bi bi-plus-circle"></i> New Post
                </a>
            </div>

            @if(session('success'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            </script>
            @endif


            <!-- Analytics -->

            <div class="row mb-4">

                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ $totalPosts }}</h3>
                            <p>Total Posts</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ $publishedPosts }}</h3>
                            <p>Published</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning">
                        <div class="card-body text-center">
                            <h3>{{ $draftPosts }}</h3>
                            <p>Draft Posts</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h3>{{ $todayPosts }}</h3>
                            <p>Today's Posts</p>
                        </div>
                    </div>
                </div>

            </div>


            <!-- Live Search -->

            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <input type="text" id="title" class="form-control" placeholder="🔍 Search title">
                </div>

                <div class="col-md-4">
                    <input type="date" id="date" class="form-control">
                </div>

                <div class="col-md-4">
                    <select id="status" class="form-select">
                        <option value="">Status</option>
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                    </select>
                </div>

            </div>


            <div id="loader">
                Loading...
            </div>

            <div id="post-list">
                @include('posts.partials.post_list')
            </div>

        </div>

    </div>


    <script>
        function bindDelete() {

            document.querySelectorAll('.delete-form').forEach(form => {

                form.addEventListener('submit', function(e) {

                    e.preventDefault();

                    Swal.fire({
                        title: 'Delete this post?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Yes'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });

        }

        bindDelete();


        const title = document.getElementById('title');
        const date = document.getElementById('date');
        const status = document.getElementById('status');
        const loader = document.getElementById('loader');
        const postList = document.getElementById('post-list');

        let timer;

        function fetchPosts() {

            loader.style.display = 'block';

            fetch(`/posts/search?title=${title.value}&status=${status.value}&date=${date.value}`)

                .then(res => res.text())

                .then(data => {

                    postList.innerHTML = data;

                    loader.style.display = 'none';

                    bindDelete();

                });

        }

        title.addEventListener('keyup', () => {

            clearTimeout(timer);

            timer = setTimeout(fetchPosts, 300);

        });

        date.addEventListener('change', fetchPosts);

        status.addEventListener('change', fetchPosts);
    </script>

</body>

</html>