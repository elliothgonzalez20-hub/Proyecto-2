<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\hasmany;


class Asignaturas extends Model
{
    use HasFactory;
    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiantes::class);
    }
}
