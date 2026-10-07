<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    public function inscripciones(): hasmany
    {
        return $this->hasMany(Inscripcion::class, 'seccion_id');
    }

    public function asignatura(): hasmany
    {
        return $this->hasMany(Asignatura::class);
    }

    public function getCuposDisponiblesAttribute(): int
    {
    $inscritos = $this->inscripciones()->count();
    $disponibles = $this->cupo_maximo - $inscritos;
    return max(0, $disponibles);
    }

    protected $table = 'secciones';

    protected $guarded = [];
}
