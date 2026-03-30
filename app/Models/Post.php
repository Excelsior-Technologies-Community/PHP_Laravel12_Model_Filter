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
