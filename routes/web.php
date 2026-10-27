<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteCalificacionesController;

Route::get('/admin/reportes/boletin/{estudiante}/{seccion}', [ReporteCalificacionesController::class, 'generarBoletin'])
    ->name('reporte.boletin')
    ->middleware(['web', 'auth']);

Route::get('/admin/reportes/historial/{estudiante}', [ReporteCalificacionesController::class, 'generarHistorialAcademico'])
    ->name('reporte.historial')
    ->middleware(['web', 'auth']);
