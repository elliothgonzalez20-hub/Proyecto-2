<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Periodo extends Model
{
    use HasFactory;

    protected $table = 'periodos';

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    protected static function booted()
    {
        static::saving(function ($periodo) {

            if ($periodo->activo) {

                static::where('id', '!=', $periodo->id)->update(['activo' => false]);
            }
        });
    }
}