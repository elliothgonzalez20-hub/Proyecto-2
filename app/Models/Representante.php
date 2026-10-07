<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\relations\BelongsTo;
use Illuminate\Database\Eloquent\relations\HasMany;

class Representante extends Model
{
    protected $table = 'representantes';

    protected $guarded = [];
    
    use HasFactory;

    public function estudiante(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }

}
