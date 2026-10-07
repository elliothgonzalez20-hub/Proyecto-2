<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profesor extends Model
{
    public function asignatura(): HasMany
    {
        return $this->hasMany(Asignatura::class);
    }

    protected $table = 'profesores';

    protected $guarded = [];

}
