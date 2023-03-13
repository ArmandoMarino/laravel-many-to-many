<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;

    // Assegno la relazione molti a molti con i Projects al prurale
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
