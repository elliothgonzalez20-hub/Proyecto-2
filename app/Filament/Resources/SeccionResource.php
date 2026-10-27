<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeccionResource\Pages;
use App\Filament\Resources\SeccionResource\RelationManagers;
use App\Models\Seccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\HtmlString;
use App\Models\Periodo;
use App\Models\Inscripcion;

class SeccionResource extends Resource
{
    protected static ?string $model = Seccion::class;
    protected static ?string $navigationLabel = 'Secciones';
    protected static ?string $navigationGroup = 'Estructura Académica';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grado_ano')
                    ->Label('Año')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('letra')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cupo_maximo')
                    ->required()
                    ->numeric()
                    ->default(35),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grado_ano')
                    ->Label('Año / Grado')
                    ->formatStateUsing(fn ($state) => "{$state}° Año")
                    ->searchable(),
                Tables\Columns\TextColumn::make('letra')
                    ->label('Sección')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cupo_maximo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inscripciones_count')
                    ->label('Estudiantes Inscritos')
                    ->counts('inscripciones')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
            
                        $periodoActivoId = Periodo::where('activo', true)->value('id');
                
                        if (!$periodoActivoId) {
                            return 0;
                        }
                
                        return \App\Models\Inscripcion::where('seccion_id', $record->id)
                            ->where('periodo_id', $periodoActivoId)
                            ->count();
                    })
                    ->action(
                        Action::make('verEstudiantes')
                            ->label('Ver Estudiantes')
                            ->modalHeading(fn ($record) => "Estudiantes en la Sección {$record->letra} ({$record->grado_ano}° Año)")
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Cerrar')
                            ->modalContent(function ($record) {
                               
                                $periodoActivoId = \App\Models\Periodo::where('activo', true)->value('id');
                        
                                $inscripciones = $record->inscripciones()
                                    ->with('estudiante')
                                    ->where('periodo_id', $periodoActivoId)
                                    ->get();
                        
                                if ($inscripciones->isEmpty()) {
                                    return new HtmlString('<p class="text-gray-500 py-4 text-center">No hay estudiantes inscritos en esta sección todavía.</p>');
                                }
                        
                                $html = '<div class="overflow-x-auto"><table class="w-full text-left border-collapse"><thead class="border-b border-gray-700"><tr><th class="py-2 px-3">Cédula</th><th class="py-2 px-3">Estudiante</th></tr></thead><tbody>';
                        
                                foreach ($inscripciones as $inscripcion) {
                                    $estudiante = $inscripcion->estudiante;
                                    $cedula = $estudiante->cedula ?? 'N/A';
                                    $nombre = trim(
                                        ($estudiante->name ?? $estudiante->nombres ?? '') . ' ' . 
                                        ($estudiante->apellidos ?? $estudiante->apellido ?? '')
                                    );
                                    $html .= "<tr class='border-b border-gray-800'><td class='py-2 px-3'>{$cedula}</td><td class='py-2 px-3'>{$nombre}</td></tr>";
                                }
                                
                                $html .= '</tbody></table></div>';
                        
                                return new HtmlString($html);
                            })
                        ),
                Tables\Columns\TextColumn::make('cupos_disponibles')
                    ->label('Cupos Disponibles')
                    ->getStateUsing(fn ($record) => $record->cupos_disponibles)
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $periodoActivoId = Periodo::where('activo', true)->value('id');
                        if (!$periodoActivoId) return $record->cupo_maximo;
                
                        $inscritosActivos = Inscripcion::where('seccion_id', $record->id)
                            ->where('periodo_id', $periodoActivoId)
                            ->count();
                
                        return $record->cupo_maximo - $inscritosActivos;
                    })
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeccions::route('/'),
            'create' => Pages\CreateSeccion::route('/create'),
            'edit' => Pages\EditSeccion::route('/{record}/edit'),
        ];
    }
}
