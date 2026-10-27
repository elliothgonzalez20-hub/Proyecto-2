<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\Seccion;
use App\Models\Calificacion;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteCalificacionesController extends Controller
{
    public function generarBoletin($estudianteId, $seccionId)
    {
        $estudiante = Estudiante::findOrFail($estudianteId);
        $seccion = Seccion::findOrFail($seccionId);
        
        $calificaciones = Calificacion::where('estudiante_id', $estudianteId)
            ->where('seccion_id', $seccionId)
            ->with('asignatura')
            ->get();

        $materias = $calificaciones->groupBy('asignatura_id');

        $data = [
            'estudiante' => $estudiante,
            'seccion' => $seccion,
            'materias' => $materias,
            'fecha' => date('d/m/Y')
        ];

        $pdf = Pdf::loadView('pdf.boletin-calificaciones', $data);
        

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("Boletin_{$estudiante->cedula}.pdf");
    }

    public function generarHistorialAcademico($estudianteId)
    {
        $estudiante = Estudiante::findOrFail($estudianteId);
        
        $calificaciones = Calificacion::where('estudiante_id', $estudianteId)
            ->with(['asignatura', 'periodo', 'seccion'])
            ->get()
            ->groupBy('periodo_id');

        $data = [
            'estudiante' => $estudiante,
            'periodosCalificaciones' => $calificaciones,
            'fecha' => date('d/m/Y')
        ];

        $pdf = Pdf::loadView('pdf.historial-academico', $data);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream("Historial_Academico_{$estudiante->cedula}.pdf");
    }
}
