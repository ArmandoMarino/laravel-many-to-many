<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    // Relazione one to one con UserDetail
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // FUnzione pubblica che restiruisce il nome intero interpolato
    public function getFullName()
    {
        return "$this->first_name  $this->last_name";
    }
}
