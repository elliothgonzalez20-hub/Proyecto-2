<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignaturaResource\Pages;
use App\Filament\Resources\AsignaturaResource\RelationManagers;
use App\Models\Asignatura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use App\Models\Profesor;

class AsignaturaResource extends Resource
{
    protected static ?string $model = Asignatura::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nombre')
                ->label('Nombre de la Asignatura')
                ->options([
                    'Matemáticas' => 'Matemáticas',
                    'Castellano' => 'Castellano y Literatura',
                    'Inglés' => 'Inglés y Otras Lenguas Extranjeras',
                    'Física' => 'Física',
                    'Química' => 'Química',
                    'Biología' => 'Biología, Ambiente y Tecnología',
                    'Geografía, Historia y Ciudadanía' => 'Geografía, Historia y Ciudadanía',
                    'Educación Física' => 'Educación Física',
                    'Orientación y Convivencia' => 'Orientación y Convivencia',
                    'Arte y Patrimonio' => 'Arte y Patrimonio',
                ])
                ->live()
                ->required()
                ->searchable(),
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('grado_ano')
                    ->label('Año / Grado')
                    ->options([
                        1 => '1° Año',
                        2 => '2° Año',
                        3 => '3° Año',
                        4 => '4° Año',
                        5 => '5° Año',
                    ])
                    ->required(),
                Forms\Components\Select::make('profesor_id')
                    ->label('Profesor Especialista')
                    ->options(function (Get $get) {
                        $nombreMateria = $get('nombres');
    
                        if (!$nombreMateria) {
                            return Profesor::all()->pluck('nombres', 'id');
                        }

                        return Profesor::where('especialidad', 'LIKE', "%{$nombreMateria}%")
                            ->get()
                            ->pluck('nombre', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Se muestran los profesores con la especialidad afín a esta asignatura.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grado_ano')
                    ->Label('Año')
                    ->searchable(),
                Tables\Columns\TextColumn::make('profesor.nombres')
                    ->Label('Profesor asignado')
                    ->default('Sin asignar')
                    ->sortable(),
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
            'index' => Pages\ListAsignaturas::route('/'),
            'create' => Pages\CreateAsignatura::route('/create'),
            'edit' => Pages\EditAsignatura::route('/{record}/edit'),
        ];
    }
}
