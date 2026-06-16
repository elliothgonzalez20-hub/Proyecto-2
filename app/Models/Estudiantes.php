<?php

namespace App\Models;

use illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Relations\BelongsTo;
use illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Estudiantes extends Model
{
    use HasFactory;
    public function representante(): BelongsTo
    {
        return $this->belongsTo(Representantes::class, 'representante_id');
    }

    public function asignaturas(): hasmany
    {
        return $this->hasMany(Asignaturas::class);
    }

    use SoftDeletes;

     protected $fillable = [
    'name',
    'apellidos',
    'nacionalidad',
    'cedula',
    'nacimiento', 
    'genero',
    'lugar',
    'representante_id',
     ];
}
