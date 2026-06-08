<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\relations\BelongsTo;
use Illuminate\Database\Eloquent\relations\HasMany;

class Representantes extends Model
{
    use HasFactory;

    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiantes::class);
    }
}
