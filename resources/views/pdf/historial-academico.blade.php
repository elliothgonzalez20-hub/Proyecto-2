<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificación de Calificaciones</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a365d; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 13px; text-transform: uppercase; color: #1a365d; }
        .header h3 { margin: 3px 0; font-size: 11px; font-weight: normal; color: #333; }
        .header p { margin: 2px 0; font-size: 9px; color: #666; }
        
        .title-box { background-color: #1a365d; color: white; text-align: center; font-weight: bold; padding: 5px; font-size: 10px; margin-bottom: 15px; text-transform: uppercase; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        .info-table td { padding: 3px; }

        .period-title { font-weight: bold; font-size: 11px; color: #1a365d; margin-top: 15px; margin-bottom: 5px; border-bottom: 1px solid #1a365d; padding-bottom: 2px; }
        
        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .grades-table th, .grades-table td { border: 1px solid #ccc; padding: 4px; text-align: center; font-size: 10px; }
        .grades-table th { background-color: #f1f5f9; color: #1a365d; }
        .grades-table td.materia { text-align: left; }

        .signatures { margin-top: 50px; width: 100%; text-align: center; }
        .signature-box { display: inline-block; width: 45%; border-top: 1px solid #333; padding-top: 5px; font-size: 9px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REPÚBLICA BOLIVARIANA DE VENEZUELA</h2>
        <h3>MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</h3>
        <p>Unidad Educativa / Liceo Simón Bolívar - Control de Estudios</p>
    </div>

    <div class="title-box">Certificación Oficial de Calificaciones (Historial Académico)</div>

    <table class="info-table">
        <tr>
            <td><strong>Apellidos y Nombres:</strong> {{ $estudiante->apellidos ?? $estudiante->apellidos }}, {{ $estudiante->name ?? $estudiante->name }}</td>
            <td><strong>Cédula:</strong> {{ $estudiante->cedula }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Fecha de Emisión:</strong> {{ $fecha }}</td>
        </tr>
    </table>

    @foreach($periodosCalificaciones as $periodoId => $calificacionesPeriodo)
        @php
            $periodoNombre = $calificacionesPeriodo->first()->periodo->nombre ?? 'Periodo Escolar';
            $materias = $calificacionesPeriodo->groupBy('asignatura_id');
        @endphp

        <div class="period-title">Período: {{ $periodoNombre }}</div>

        <table class="grades-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Asignatura</th>
                    <th>1er Lapso</th>
                    <th>2do Lapso</th>
                    <th>3er Lapso</th>
                    <th>Definitiva</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materias as $asignaturaId => $items)
                    @php
                        $asignatura = $items->first()->asignatura;
                        $l1 = $items->where('lapso', 1)->first()->nota ?? '-';
                        $l2 = $items->where('lapso', 2)->first()->nota ?? '-';
                        $l3 = $items->where('lapso', 3)->first()->nota ?? '-';
                        
                        $notasValidas = $items->pluck('nota')->filter();
                        $definitiva = $notasValidas->count() > 0 ? round($notasValidas->avg(), 2) : '-';
                    @endphp
                    <tr>
                        <td class="materia">{{ $asignatura->nombre ?? 'Asignatura' }}</td>
                        <td>{{ $l1 }}</td>
                        <td>{{ $l2 }}</td>
                        <td>{{ $l3 }}</td>
                        <td><strong>{{ $definitiva }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-box">Control de Estudios</div>
            </td>
            <td>
                <div class="signature-box">Dirección del Plantel (Sello)</div>
            </td>
        </tr>
    </table>

</body>
</html>