<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterPreset extends Model
{
    protected $fillable = ['name', 'filters'];

    protected $casts = ['filters' => 'array'];
}
