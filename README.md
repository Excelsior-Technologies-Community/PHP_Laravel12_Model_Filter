# PHP_Laravel12_Model_Filter



## Project Description

The PHP_Laravel12_Model_Filter is a Laravel 12-based web application designed to manage posts efficiently with advanced filtering, searching, and sorting capabilities.

It leverages the Lacodix Laravel Model Filter package to implement dynamic and easy-to-use filters, enabling users to query posts based on multiple criteria such as title, status, and date.

This project demonstrates a clean MVC structure, professional UI design, and modern front-end features for CRUD (Create, Read, Update, Delete) operations with responsive design and interactive elements.

In short, it’s a full-stack Laravel CRUD application with enhanced model filtering.



### Key Features

- CRUD Posts: Create, Read, Update, Delete
- Filter & Search: Title, Status, Date
- Sort Posts: By title or creation date
- Responsive UI: Bootstrap 5, SweetAlert2, Flatpickr
- Modular Filters: Easy to extend




## Technologies

- Laravel 12, 
- PHP, 
- MySQL
- Lacodix Laravel Model Filter package
- Bootstrap 5, 
- SweetAlert2, 
- Flatpickr
- Blade Templates


## Use Case

Ideal for content management systems, demonstrating Laravel CRUD, filtering, and responsive UI.



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Model_Filter "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Model_Filter

```

#### Explanation:

Installs a fresh Laravel 12 project and moves into the project folder.




## STEP 2: Database Setup 

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_model_filter
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_model_filter

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

Connects Laravel to MySQL and creates default tables for the project.




## STEP 3: Install Model Filter Package

### Run:

```
composer require lacodix/laravel-model-filter

```

#### Explanation:

Installs a package that allows filtering, searching, and sorting Eloquent models easily.




## STEP 4: Create Model + Migration

### Run:

```
php artisan make:model Post -m

```

### Edit migration in database/migrations/xxxx_create_posts_table.php:

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_published')->default(1);
            $table->date('post_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

```

### Then Run:

```
php artisan migrate

```


### app/Models/Post.php

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lacodix\LaravelModelFilter\Traits\HasFilters;
use Lacodix\LaravelModelFilter\Traits\IsSearchable;
use Lacodix\LaravelModelFilter\Traits\IsSortable;

use App\Models\Filters\TitleFilter;
use App\Models\Filters\PublishedFilter;
use App\Models\Filters\CreatedAfterFilter;

class Post extends Model
{
    use HasFilters, IsSearchable, IsSortable;

    protected $fillable = [
        'title',
        'content',
        'is_published',
        'post_date'
    ];

    // Filters
    protected array $filters = [
        TitleFilter::class,
        PublishedFilter::class,
        CreatedAfterFilter::class,
    ];

    // Search fields
    protected array $searchable = [
        'title',
        'content',
    ];

    // Sort fields
    protected array $sortable = [
        'title',
        'created_at',
    ];
}

```

#### Explanation:

Creates the Post model and database table, defines filters, search, and sorting fields.




## STEP 5: Create Filters

### Package provides artisan command:

```
php artisan make:filter TitleFilter --type=string --field=title
php artisan make:filter PublishedFilter --type=boolean --field=is_published
php artisan make:filter CreatedAfterFilter --type=date --field=created_at

```

### Filters are separate classes inside:

```
app/Models/Filters/

```


#### Explanation:

Generates filter classes to filter posts by title, published status, or creation date.





## STEP 6: Update Filters

### app/Models/Filters/TitleFilter.php:

```
<?php

namespace App\Models\Filters;

use Lacodix\LaravelModelFilter\Filters\StringFilter;

class TitleFilter extends StringFilter
{
    protected string $field = 'title';
}

```


### app/Models/Filters/PublishedFilter.php:

```
<?php

namespace App\Models\Filters;

use Lacodix\LaravelModelFilter\Filters\BooleanFilter;

class PublishedFilter extends BooleanFilter
{
    protected string $field = 'is_published';
}

```


### app/Models/Filters/CreatedAfterFilter.php:

```
<?php

namespace App\Models\Filters;

use Lacodix\LaravelModelFilter\Filters\DateFilter;
use Lacodix\LaravelModelFilter\Enums\FilterMode;

class CreatedAfterFilter extends DateFilter
{
    public FilterMode $mode = FilterMode::GREATER_OR_EQUAL;

    protected string $field = 'created_at';
}

```


#### Explanation:

Each filter class tells Laravel which field to filter and how to apply the filter.




## STEP 7: Create Controller

### Run: 

```
php artisan make:controller PostController

```

### app/Http/Controllers/PostController.php:

```
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

```



#### Explanation:

Handles displaying, creating, editing, updating, deleting posts, and applying filters.





## STEP 8: Add Routes

### routes/web.php:

```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return redirect()->route('posts.index'); // Redirect to posts dashboard
});

// ✅ Resource route handles index, create, store, edit, update, destroy
Route::resource('posts', PostController::class);

```

#### Explanation:

Defines URLs to access dashboard, create, edit, and delete posts.





## STEP 9: Create Blade View

### resources/views/posts/index.blade.php

```
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

```



### resources/views/posts/create.blade.php

```
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

```


### resources/views/posts/edit.blade.php

```
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

```



#### Explanation:

Views provide user interface for dashboard, create/edit forms, and filter/search posts.






## STEP 10: Run Application  

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000

```

#### Explanation:

Starts Laravel development server and opens your project in the browser.




## Expected Output:


### Posts Dashboard:


<img src="screenshots/Screenshot 2026-03-30 123506.png" width="900">


### Add New Post:


<img src="screenshots/Screenshot 2026-03-30 123556.png" width="900">


### Post Created Successfully:


<img src="screenshots/Screenshot 2026-03-30 123609.png" width="900">


### Posts Overview:


<img src="screenshots/Screenshot 2026-03-30 132750.png" width="900">


### Search by Title:


<img src="screenshots/Screenshot 2026-03-30 132809.png" width="900">


### Filter by Date / Created At:


<img src="screenshots/Screenshot 2026-03-30 132829.png" width="900">


### Filter by Status:


<img src="screenshots/Screenshot 2026-03-30 132847.png" width="900">


### Edit Post Details:

<img src="screenshots/Screenshot 2026-03-30 132926.png" width="900">

<img src="screenshots/Screenshot 2026-03-30 132952.png" width="900">


### Delete Post:


<img src="screenshots/Screenshot 2026-03-30 133025.png" width="900">



---

## Project Folder Structure:

```
PHP_Laravel12_Model_Filter/
│
├── app/
│   ├── Models/
│   │   ├── Post.php
│   │   └── Filters/
│   │       ├── TitleFilter.php
│   │       ├── PublishedFilter.php
│   │       └── CreatedAfterFilter.php
│   └── Http/Controllers/
│       └── PostController.php
│
├── database/migrations/
│   └── xxxx_create_posts_table.php
│
├── resources/views/posts/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
│
├── routes/web.php
├── .env
├── composer.json
└── artisan

```
