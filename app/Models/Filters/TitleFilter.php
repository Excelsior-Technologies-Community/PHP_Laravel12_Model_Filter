<?php

namespace App\Models\Filters;

use Lacodix\LaravelModelFilter\Filters\StringFilter;

class TitleFilter extends StringFilter
{
    protected string $field = 'title';
}