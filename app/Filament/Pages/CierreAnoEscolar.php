<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use App\Models\Seccion;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Calificacion;
use App\Models\Periodo;
use App\Models\Inscripcion;
use Filament\Forms\Get;

class CierreAnoEscolar extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationGroup = 'Procesos Escolares';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Cierre y Definitivas';
    protected static string $view = 'filament.pages.cierre-ano-escolar';

    public ?int $grado_ano = null;
    public ?int $seccion_id = null;
    public ?int $asignatura_id = null;

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
            
                Select::make('grado_ano')
                    ->label('Año / Grado')
                    ->options([
                        1 => '1er Año',
                        2 => '2do Año',
                        3 => '3er Año',
                        4 => '4to Año',
                        5 => '5to Año',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set) {

                        $set('seccion_id', null);
                        $set('asignatura_id', null);
                    }),

                Select::make('seccion_id')
                    ->label('Sección')
                    ->options(function (Get $get) {
                        $anio = $get('grado_ano');
                        if (!$anio) {
                            return [];
                        }
    
                        return Seccion::where('grado_ano', $anio)
                            ->orWhere('grado_ano', $anio)
                            ->get()
                            ->mapWithKeys(function ($item) {
                                $nombreSeccion = $item->seccion ?? $item->letra ?? 'Única';
                                return [$item->id => "Sección {$nombreSeccion}"];
                            });
                    })
                    ->required()
                    ->live()
                    ->disabled(fn (Get $get) => !$get('grado_ano'))
                    ->afterStateUpdated(fn (callable $set) => $set('asignatura_id', null)),

                Select::make('asignatura_id')
                    ->label('Asignatura')
                    ->options(function (\Filament\Forms\Get $get) {
                        $seccionId = $get('seccion_id');
                        if (!$seccionId) return [];
                        $seccion = Seccion::find($seccionId);
                        if (!$seccion) return [];
                        $anioSeccion = $seccion->grado_ano ?? $seccion->grado_ano;

                        return Asignatura::where('grado_ano', $anioSeccion)
                            ->get()
                            ->mapWithKeys(fn ($item) => [$item->id => "{$item->nombre} ({$item->codigo})"]);
                    })
                    ->required()
                    ->live()
                    ->disabled(fn (\Filament\Forms\Get $get) => !$get('seccion_id'))
                    ->afterStateUpdated(fn ($livewire) => $livewire->resetTable()),
            ])
        ]);
    }

    public function table(Table $table): Table
{
    $periodoActivoId = Periodo::where('activo', true)->value('id');

    if (!$this->seccion_id || !$this->asignatura_id || !$periodoActivoId) {
        return $table->query(Estudiante::query()->whereRaw('1 = 0'));
    }

    return $table
        ->query(
            Estudiante::query()
                ->whereIn('id', function ($query) use ($periodoActivoId) {
                    $query->select('estudiante_id')
                          ->from('inscripciones')
                          ->where('seccion_id', $this->seccion_id)
                          ->where('periodo_id', $periodoActivoId); // <--- Filtro estricto por periodo activo
                })
        )
        ->columns([
            TextColumn::make('cedula')
                ->label('Cédula / Identidad')
                ->searchable(),
            
            TextColumn::make('name')
                ->label('Apellidos y Nombres')
                ->getStateUsing(fn ($record) => "{$record->apellidos}, {$record->name}"),

            TextColumn::make('l1')
                ->label('1er Lapso')
                ->getStateUsing(fn ($record) => $this->getNotaLapso($record->id, 1)),

            TextColumn::make('l2')
                ->label('2do Lapso')
                ->getStateUsing(fn ($record) => $this->getNotaLapso($record->id, 2)),

            TextColumn::make('l3')
                ->label('3er Lapso')
                ->getStateUsing(fn ($record) => $this->getNotaLapso($record->id, 3)),

            TextColumn::make('definitiva')
                ->label('Nota Definitiva')
                ->getStateUsing(function ($record) {
                    $n1 = $this->getNotaLapsoRaw($record->id, 1);
                    $n2 = $this->getNotaLapsoRaw($record->id, 2);
                    $n3 = $this->getNotaLapsoRaw($record->id, 3);
                    
                    $notas = array_filter([$n1, $n2, $n3], fn($n) => $n !== null);
                    if (count($notas) == 0) return '-';
                    
                    return round(array_sum($notas) / count($notas), 2);
                })
                ->weight('bold'),

            TextColumn::make('estado')
                ->label('Condición')
                ->badge()
                ->getStateUsing(function ($record) {
                    $n1 = $this->getNotaLapsoRaw($record->id, 1);
                    $n2 = $this->getNotaLapsoRaw($record->id, 2);
                    $n3 = $this->getNotaLapsoRaw($record->id, 3);
                    
                    $notas = array_filter([$n1, $n2, $n3], fn($n) => $n !== null);
                    if (count($notas) < 3) return 'En Proceso';
                    
                    $promedio = array_sum($notas) / count($notas);
                    return $promedio >= 10 ? 'Aprobado' : 'Aplazado';
                })
                ->color(fn (string $state): string => match ($state) {
                    'Aprobado' => 'success',
                    'Aplazado' => 'danger',
                    default => 'warning',
                }),
        ])
        ->paginated(false);
}

private function getNotaLapso($estudianteId, $lapso) {
    $nota = $this->getNotaLapsoRaw($estudianteId, $lapso);
    return $nota !== null ? ($nota + 0) : '-';
}

private function getNotaLapsoRaw($estudianteId, $lapso) {
    $periodoActivoId = \App\Models\Periodo::where('activo', true)->value('id');

    $cal = \App\Models\Calificacion::where('estudiante_id', $estudianteId)
        ->where('seccion_id', $this->seccion_id)
        ->where('asignatura_id', $this->asignatura_id)
        ->where('lapso', $lapso)
        ->first();
        
    return $cal ? $cal->nota : null;
}
}
