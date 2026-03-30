<?php

namespace App\Models\Filters;

use Lacodix\LaravelModelFilter\Filters\BooleanFilter;

class PublishedFilter extends BooleanFilter
{
    protected string $field = 'is_published';
}