<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'department', 'curator_id', 'average_score'];

    public function curator()
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
