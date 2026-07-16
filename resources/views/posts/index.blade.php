<!DOCTYPE html>
<html>
<head>
    <title>Posts Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: linear-gradient(135deg, #0f172a, #1e293b); color: #e2e8f0; font-family: 'Segoe UI', sans-serif; }
        .main-card { background: rgba(255,255,255,.05); padding: 25px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,.5); }
        .post-card { background: rgba(255,255,255,.03); padding: 15px; border-radius: 15px; transition: .3s; }
        .post-card:hover { background: rgba(255,255,255,.08); transform: scale(1.01); }
        input, select, textarea { background: #0f172a !important; border: 1px solid #334155 !important; color: white !important; }
        input::placeholder { color: #94a3b8 !important; }
        .preset-badge { cursor: pointer; font-size: 13px; }
        .pagination .page-link { background: #1e293b; border-color: #334155; color: #e2e8f0; }
        .pagination .page-item.active .page-link { background: #6366f1; border-color: #6366f1; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
<div class="main-card">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="bi bi-speedometer2"></i> Posts Dashboard
            <span class="badge bg-primary ms-2" title="Total Posts">{{ $totalPosts }} Posts</span>
        </h3>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Post
        </a>
    </div>

    @if(session('success'))
    <script>
        Swal.fire({ toast: true, position: 'top-end', icon: 'success',
            title: '{{ session('success') }}', showConfirmButton: false, timer: 2500 });
    </script>
    @endif

    <!-- STATS -->
    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="card bg-primary text-white text-center p-3">
                <h3>{{ $totalPosts }}</h3><small>Total Posts</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-success text-white text-center p-3">
                <h3>{{ $publishedPosts }}</h3><small>Published</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-warning text-dark text-center p-3">
                <h3>{{ $draftPosts }}</h3><small>Drafts</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-dark text-white text-center p-3">
                <h3>{{ $todayPosts }}</h3><small>Today</small>
            </div>
        </div>
    </div>

    <!-- FILTER PRESETS -->
    @if($presets->count())
    <div class="mb-3">
        <small class="text-muted">📌 Saved Presets:</small>
        <div class="d-flex flex-wrap gap-2 mt-1">
            @foreach($presets as $preset)
            <span class="badge bg-indigo preset-badge d-flex align-items-center gap-1"
                  style="background:#6366f1"
                  onclick="loadPreset({{ json_encode($preset->filters) }})"
                  title="Click to load">
                <i class="bi bi-bookmark-fill"></i> {{ $preset->name }}
            </span>
            <form action="{{ route('presets.delete', $preset->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger py-0 px-1" title="Delete preset" style="font-size:11px">✕</button>
            </form>
            @endforeach
        </div>
    </div>
    @endif

    <!-- FILTER FORM -->
    <form method="GET" action="{{ route('posts.index') }}" id="filterForm" class="row g-2 mb-2">

        <div class="col-md-3">
            <input type="text" name="title_filter" id="title_filter" class="form-control"
                   placeholder="🔍 Search title" value="{{ request('title_filter') }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="created_after_filter" id="created_after_filter" class="form-control"
                   value="{{ request('created_after_filter') }}">
        </div>

        <div class="col-md-2">
            <select name="published_filter" id="published_filter" class="form-select">
                <option value="">All Status</option>
                <option value="1" {{ request('published_filter') == '1' ? 'selected' : '' }}>Published</option>
                <option value="0" {{ request('published_filter') == '0' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="sort" id="sort" class="form-select">
                <option value="latest"     {{ request('sort','latest') == 'latest'     ? 'selected' : '' }}>🕒 Latest</option>
                <option value="oldest"     {{ request('sort') == 'oldest'     ? 'selected' : '' }}>🕒 Oldest</option>
                <option value="title_asc"  {{ request('sort') == 'title_asc'  ? 'selected' : '' }}>🔤 Title A-Z</option>
                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>🔤 Title Z-A</option>
            </select>
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-funnel"></i>
            </button>
        </div>

        <div class="col-md-1">
            <a href="{{ route('posts.index') }}" class="btn btn-secondary w-100" title="Clear Filters">
                <i class="bi bi-x-circle"></i>
            </a>
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-outline-warning w-100" title="Save as Preset"
                    data-bs-toggle="modal" data-bs-target="#presetModal">
                <i class="bi bi-bookmark-plus"></i>
            </button>
        </div>

    </form>

    <!-- POSTS LIST -->
    <div id="post-list">
        @include('posts.partials.post_list')
    </div>

</div>
</div>

<!-- SAVE PRESET MODAL -->
<div class="modal fade" id="presetModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h6 class="modal-title"><i class="bi bi-bookmark-plus"></i> Save Filter Preset</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('presets.save') }}" id="presetForm">
                @csrf
                <input type="hidden" name="title_filter"         id="p_title">
                <input type="hidden" name="created_after_filter" id="p_date">
                <input type="hidden" name="published_filter"     id="p_status">
                <input type="hidden" name="sort"                 id="p_sort">
                <div class="modal-body">
                    <input type="text" name="preset_name" class="form-control" placeholder="Preset name e.g. Draft Posts" required>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-warning btn-sm">💾 Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Bind delete confirm
    function bindDelete() {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete this post?', icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete!'
                }).then(r => { if (r.isConfirmed) form.submit(); });
            });
        });
    }
    bindDelete();

    // Load preset into form fields
    function loadPreset(filters) {
        document.getElementById('title_filter').value         = filters.title_filter         ?? '';
        document.getElementById('created_after_filter').value = filters.created_after_filter ?? '';
        document.getElementById('published_filter').value     = filters.published_filter     ?? '';
        document.getElementById('sort').value                 = filters.sort                 ?? 'latest';
        document.getElementById('filterForm').submit();
    }

    // Fill hidden inputs before saving preset
    document.getElementById('presetModal').addEventListener('show.bs.modal', function () {
        document.getElementById('p_title').value  = document.getElementById('title_filter').value;
        document.getElementById('p_date').value   = document.getElementById('created_after_filter').value;
        document.getElementById('p_status').value = document.getElementById('published_filter').value;
        document.getElementById('p_sort').value   = document.getElementById('sort').value;
    });
</script>

</body>
</html>
