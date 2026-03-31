<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'type',
        'color'
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
    ];
}
