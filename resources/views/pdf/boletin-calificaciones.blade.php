<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletín de Calificaciones</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { position: relative; text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1a365d; padding-bottom: 10px; min-height: 55px; }
        .header-logo { position: absolute; left: 0; top: 0; height: 50px; width: auto; }
        .header h2 { margin: 0; font-size: 14px; text-transform: uppercase; color: #1a365d; }
        .header h3 { margin: 3px 0; font-size: 12px; font-weight: normal; color: #333; }
        .header p { margin: 2px 0; font-size: 9px; color: #666; }
        
        .title-box { background-color: #1a365d; color: white; text-align: center; font-weight: bold; padding: 6px; font-size: 11px; margin-bottom: 15px; text-transform: uppercase; }
        
        .info-table, .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 4px; font-size: 11px; }
        
        .grades-table th, .grades-table td { border: 1px solid #ccc; padding: 6px; text-align: center; font-size: 11px; }
        .grades-table th { background-color: #f1f5f9; color: #1a365d; }
        .grades-table td.materia { text-align: left; font-weight: bold; }

        .signatures { margin-top: 60px; width: 100%; text-align: center; }
        .signature-box { display: inline-block; width: 45%; border-top: 1px solid #333; padding-top: 5px; font-size: 10px; }
    </style>
</head>
<body>

    <!-- Encabezado Institucional con Cintillo -->
    <div class="header">
        @php
            $imagePath = public_path('images/cintillo.png');
            if (!file_exists($imagePath)) { $imagePath = public_path('images/cintillo.jpg'); }
        @endphp
        @if(file_exists($imagePath))
            <img src="{{ $imagePath }}" class="header-logo" alt="Cintillo MPPE">
        @endif
        <h2>REPÚBLICA BOLIVARIANA DE VENEZUELA</h2>
        <h3>MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</h3>
        <p>Unidad Educativa / Liceo Simón Bolívar - Control de Estudios</p>
    </div>

    <div class="title-box">Informe Descriptivo y Calificaciones del Rendimiento Estudiantil</div>

    <!-- Datos del Estudiante -->
    <table class="info-table">
        <tr>
            <td style="width: 60%;"><strong>Apellidos y Nombres:</strong> {{ $estudiante->apellidos ?? $estudiante->apellidos }}, {{ $estudiante->name ?? $estudiante->name }}</td>
            <td style="width: 40%;"><strong>Cédula Escolar / Identidad:</strong> {{ $estudiante->cedula }}</td>
        </tr>
        <tr>
            <td><strong>Año / Grado:</strong> {{ $seccion->grado_ano ?? $seccion->grado_ano }} - <strong>Sección:</strong> {{ $seccion->seccion ?? $seccion->letra }}</td>
            <td><strong>Fecha de Emisión:</strong> {{ $fecha }}</td>
        </tr>
    </table>

    <!-- Tabla de Calificaciones por Lapsos -->
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
                    
                    // Cálculo simple de la definitiva si están los tres lapsos cargados
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

    <!-- Firmas y Sellos -->
    <table class="signatures">
        <tr>
            <td>
                <div class="signature-box">
                    Firma y Sello del Docente Guía
                </div>
            </td>
            <td>
                <div class="signature-box">
                    Control de Estudios / Dirección
                </div>
            </td>
        </tr>
    </table>

</body>
</html>