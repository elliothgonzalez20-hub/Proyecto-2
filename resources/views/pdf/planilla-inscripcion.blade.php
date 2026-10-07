<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla de Inscripción</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .header { 
            position: relative; 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #1a365d; 
            padding-bottom: 10px; 
            min-height: 55px; 
            }

        .header-logo { 
            position: absolute; 
            left: 0; 
            top: -50; 
            height: 70px; 
            width: auto; 
            }

        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; color: #1a365d; }
        .header h3 { margin: 3px 0; font-size: 14px; font-weight: normal; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        .title-box { background-color: #1a365d; color: white; text-align: center; padding: 6px; font-weight: bold; margin-bottom: 15px; border-radius: 4px; }
        .section-title { font-weight: bold; background: #edf2f7; padding: 4px 8px; margin-top: 15px; border-left: 4px solid #1a365d; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td, th { padding: 6px 8px; text-align: left; font-size: 11px; }
        .bordered td, .bordered th { border: 1px solid #cbd5e0; }
        .footer-signatures { margin-top: 50px; width: 100%; text-align: center; }
        .signature-line { border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">

        @php
            $imagePath = public_path('images/cintillo.png');
            if (!file_exists($imagePath)) {
            $imagePath = public_path('images/cintillo.ppg');
            }
        @endphp

        @if(file_exists($imagePath))
            <img src="{{ $imagePath }}" class="header-logo" alt="Cintillo MPPE">
        @endif

            <h2>REPÚBLICA BOLIVARIANA DE VENEZUELA</h2>
            <h3>MINISTERIO DEL PODER POPULAR PARA LA EDUCACIÓN</h3>
            <p>Unidad Educativa / Liceo Simón Bolívar</p>
        
    </div>

    <div class="title-box">
        COMPROBANTE DE INSCRIPCIÓN - AÑO ESCOLAR {{ $inscripcion->periodo_academico ?? date('Y') . '-' . (date('Y')+1) }}
    </div>

    <div class="section-title">DATOS DEL ESTUDIANTE</div>
    <table>
        <tr>
            <td><strong>Nombres y Apellidos:</strong> {{ $inscripcion->estudiante->name ?? '' }} {{ $inscripcion->estudiante->apellidos ?? $inscripcion->estudiante->nombre ?? '' }}</td>
            <td><strong>Cédula:</strong> {{ $inscripcion->estudiante->cedula ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Nacimiento:</strong> {{ $inscripcion->estudiante->nacimiento ?? 'N/A' }}</td>
            <td><strong>Sexo:</strong> {{ $inscripcion->estudiante->genero ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">DATOS ACADÉMICOS DE LA INSCRIPCIÓN</div>
    <table>
        <tr>
            <td><strong>Año / Grado:</strong> {{ $inscripcion->seccion->grado_ano ?? 'N/A' }}° Año</td>
            <td><strong>Sección:</strong> {{ $inscripcion->seccion->letra ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Inscripción:</strong> {{ $inscripcion->created_at ? $inscripcion->created_at->format('d/m/Y') : date('d/m/Y') }}</td>
            <td><strong>Estado:</strong> Inscrito / Activo</td>
        </tr>
    </table>

    <div class="section-title" style="margin-top: 40px;">FIRMAS Y SELLOS</div>
    <table class="footer-signatures">
        <tr>
            <td style="width: 50%;">
                <div class="signature-line">Firma del Representante</div>
            </td>
            <td style="width: 50%;">
                <div class="signature-line">Firma y Sello Dirección / Control de Estudios</div>
            </td>
        </tr>
    </table>

</body>
</html>