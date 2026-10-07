<?php

namespace App\Models;

use illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Relations\BelongsTo;
use illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Estudiante extends Model
{
    protected $table = 'estudiantes';

    protected $guarded = [];

    public function representante(): BelongsTo
    {
        return $this->belongsTo(Representante::class, 'representante_id');
    }

    public function asignatura(): HasMany
    {
        return $this->hasMany(Asignatura::class);
    }

    public function inscripcion(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
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
