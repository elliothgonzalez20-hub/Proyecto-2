<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use App\Models\Seccion;
use App\Models\Asignatura;
use App\Models\Estudiante;
use App\Models\Calificacion;
use Filament\Forms\Get;
use App\Models\Periodo;

class CargarCalificaciones extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationGroup = 'Procesos Escolares';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Carga de Calificaciones';
    protected static string $view = 'filament.pages.cargar-calificaciones';

    public ?int $grado_ano = null;
    public ?int $seccion_id = null;
    public ?int $asignatura_id = null;
    public ?int $lapso = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)->schema([
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
                            
                            if (!$seccionId) {
                                return [];
                            }

                            $seccion = Seccion::find($seccionId);
                            if (!$seccion) {
                                return [];
                            }

                            $anioSeccion = $seccion->grado_ano ?? $seccion->grado_ano;

                            return Asignatura::where('grado_ano', $anioSeccion)
                                ->get()
                                ->mapWithKeys(fn ($item) => [$item->id => "{$item->nombre} ({$item->codigo})"]);
                        })
                        ->required()
                        ->live()
                        ->disabled(fn (\Filament\Forms\Get $get) => !$get('seccion_id'))
                        ->afterStateUpdated(fn ($livewire) => $livewire->resetTable()),

                    Select::make('lapso')
                        ->label('Lapso Evaluativo')
                        ->options([
                            1 => 'Primer Lapso',
                            2 => 'Segundo Lapso',
                            3 => 'Tercer Lapso',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($livewire) => $livewire->resetTable()),
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        if (!$this->seccion_id || !$this->asignatura_id || !$this->lapso) {
            return $table->query(Estudiante::query()->whereRaw('1 = 0'));
        }

        return $table
            ->query(
                Estudiante::query()
                    ->whereIn('id', function ($query) {
                        $query->select('estudiante_id')
                              ->from('inscripciones')
                              ->where('seccion_id', $this->seccion_id);
                    })
            )
            ->columns([
                TextColumn::make('cedula')
                    ->label('Cédula / Identificación')
                    ->searchable(),
                
                TextColumn::make('name')
                    ->label('Apellidos y Nombres')
                    ->formatStateUsing(fn ($record) => "{$record->apellidos}, {$record->name}")
                    ->searchable(['nombres', 'apellidos']),

                    TextInputColumn::make('nota_lapso')
                    ->label('Calificación (1 - 20)')
                    ->getStateUsing(function ($record) {
                        $calificacion = Calificacion::where('estudiante_id', $record->id)
                            ->where('seccion_id', $this->seccion_id)
                            ->where('asignatura_id', $this->asignatura_id)
                            ->where('lapso', $this->lapso)
                            ->first();
                        
                        if (!$calificacion) {
                            return '';
                        }

                        // Muestra el número tal cual (ej: 9.5, 14, 18.3) sin ceros decimales forzados
                        return $calificacion->nota + 0; 
                    })
                    ->updateStateUsing(function ($record, $state) {
                        $notaValida = max(1, min(20, floatval($state)));
                        $periodoActivoId = Periodo::where('activo', true)->value('id');

                        Calificacion::updateOrCreate(
                            [
                                'estudiante_id' => $record->id,
                                'seccion_id' => $this->seccion_id,
                                'asignatura_id' => $this->asignatura_id,
                                'lapso' => $this->lapso,
                            ],
                            [
                                'nota' => $notaValida,
                                'periodo_id' => $periodoActivoId
                            ]
                        );
                    })
                    ->rules(['required', 'numeric', 'min:1', 'max:20'])
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('imprimir_boletin')
                    ->label('Boletín')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('reporte.boletin', [
                        'estudiante' => $record->id, 
                        'seccion' => $this->seccion_id
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}