<?php

namespace App\Models\Filters;

use Lacodix\LaravelModelFilter\Filters\DateFilter;
use Lacodix\LaravelModelFilter\Enums\FilterMode;

class CreatedAfterFilter extends DateFilter
{
    public FilterMode $mode = FilterMode::GREATER_OR_EQUAL;

    protected string $field = 'created_at';
}
