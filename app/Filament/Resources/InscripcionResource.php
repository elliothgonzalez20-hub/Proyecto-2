<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscripcionResource\Pages;
use App\Filament\Resources\InscripcionResource\RelationManagers;
use App\Models\Inscripcion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use App\Models\Seccion;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class InscripcionResource extends Resource
{
    protected static ?string $model = Inscripcion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('estudiante_id')
                    ->relationship('estudiante', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->apellidos} - C.I: {$record->cedula}")
                    ->required()
                    ->unique(
                        table: 'inscripciones',
                        column: 'estudiante_id',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, Get $get) {
                            return $rule->where('periodo_id', $get('periodo_id'));
                        }
                    )
                    ->validationMessages([
                        'unique' => 'Este estudiante ya se encuentra inscrito en el período escolar seleccionado.',
                    ])
                    ->searchable('cedula')
                    ->preload(),
                Forms\Components\Select::make('grado_ano')
                    ->label('Año / Grado')
                    ->options([
                        1 => '1° Año',
                        2 => '2° Año',
                        3 => '3° Año',
                        4 => '4° Año',
                        5 => '5° Año',
                    ])
                    ->live()
                    ->dehydrated(false)
                    ->required(),
                
                Forms\Components\Select::make('seccion_id')
                    ->label('Sección')
                    ->options(function (Get $get) {
                        $gradoAno = $get('grado_ano');
                
                        if (!$gradoAno) {
                            return [];
                        }

                        return Seccion::where('grado_ano', $gradoAno)
                            ->get()
                            ->mapWithKeys(function ($seccion) {
                                $cupos = $seccion->cupos_disponibles;
                                $textoCupos = $cupos > 0 ? "{$cupos} cupos disponibles" : "SIN CUPOS";
                                
                                return [
                                    $seccion->id => "Sección {$seccion->letra} ({$textoCupos})"
                                ];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->disabled(fn (Get $get) => !$get('grado_ano'))
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $seccion = Seccion::find($value);
                                if ($seccion && $seccion->cupos_disponibles <= 0) {
                                    $fail('La sección seleccionada ya no tiene cupos disponibles.');
                                }
                            };
                        },
                    ]),        
                /*Forms\Components\Select::make('seccion_id')
                    ->Label('Sección')
                    ->relationship('seccion', 'letra')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->grado_ano}° Año - Sección {$record->letra}")
                    ->required()
                    ->searchable(['grado_ano', 'Letra']),*/
                Forms\Components\Select::make('periodo_id')
                    ->relationship('periodo', 'nombre')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\DatePicker::make('fecha_inscripcion')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Retirado' => 'Retirado',
                        'Promovido' => 'Promovido',
                            ])
                    ->default('Activo')
                    ->required(),
                Forms\Components\Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /*Tables\Columns\TextColumn::make('estudiante_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seccion_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodo_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inscripcion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),*/

                Tables\Columns\TextColumn::make('periodo.nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('estudiante.cedula')
                    ->label('Cédula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estudiante.name')
                    ->label('Estudiante')
                    ->formatStateUsing(fn ($record) => "{$record->estudiante->name} {$record->estudiante->apellidos}"),
                Tables\Columns\TextColumn::make('seccion.grado_ano')
                    ->label('Año/Grado'),
                Tables\Columns\TextColumn::make('seccion.letra')
                    ->label('Sección'),
                Tables\Columns\TextColumn::make('fecha_inscripcion')
                    ->date('d/m/Y'),
                Tables\Columns\BadgeColumn::make('estado')
                        ->colors([
                            'success' => 'Activo',
                            'danger' => 'Retirado',
                            'warning' => 'Promovido',
                        ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('periodo_id')
                    ->relationship('periodo', 'nombre')
                    ->label('Filtrar por Periodo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('descargarPlanilla')
                        ->label('Planilla PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($record) {
                            $pdf = Pdf::loadView('pdf.planilla-inscripcion', [
                            'inscripcion' => $record->load(['estudiante', 'seccion']),
                            ]);
                        
                            $cedula = $record->estudiante->cedula ?? $record->id;
                                    
                            return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "planilla_inscripcion_{$cedula}.pdf"
                            );
                        }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            /*'index' => Pages\ListInscripcions::route('/'),
            'create' => Pages\CreateInscripcion::route('/create'),
            'edit' => Pages\EditInscripcion::route('/{record}/edit'),*/
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInscripcions::route('/'),
            'create' => Pages\CreateInscripcion::route('/create'),
            'edit' => Pages\EditInscripcion::route('/{record}/edit'),
        ];
    }
}
