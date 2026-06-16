<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstudiantesResource\Pages;
use App\Filament\Resources\EstudiantesResource\RelationManagers;
use App\Filament\Resources\EstudiantesResource\RelationManagers\RepresentantesRelationManager;
use App\Models\Estudiantes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms\Components\Select;
use Ramsey\Uuid\Type\Integer;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
class EstudiantesResource extends Resource
{
    protected static ?string $model = Estudiantes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            
                 TextInput::make('name')
                 ->label('Nombres')
                 ->required()
                 ->maxLength('255'),

                 TextInput::make("apellidos")
                ->required()
                ->maxLength(255),

                 Select::make('nacionalidad')
                ->options([
                    'Venezonalana'=> 'Venezolana',
                    'Extranjera'=> 'Extranjera',
                ])
                ->required(),

                 TextInput::make('cedula')
                 ->label('Cédula de Identidad')
                 ->rules(['regex:/^[0-9]+$/'])
                 ->extraInputAttributes([
                    'inputmode' => 'numeric', 
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"
                        ])
                 ->validationMessages([
                    'regex' => 'La cédula no puede tener letras, espacios ni caracteres especiales.',
                        ])
                 ->unique(
                    table: 'estudiantes',
                    column: 'cedula',
                    ignoreRecord: true,
                    modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule) {
                    return $rule->where('cedula', request()->input('components.0.updates.data.cedula')); 
                     })
                        ->validationMessages([
                        'unique' => 'Esta cédula ya se encuentra registrada.',
                ])
                ->required()
                ->unique() 
                ->maxLength(255),

                DatePicker::make('nacimiento')
                ->label('Fecha de Nacimiento')
                ->required()
                ->maxDate(now()),

                Select::make('genero')
                ->label('Sexo')
                ->options([
                    'Masculino'=> 'Masculino',
                    'Femenino'=> 'Femenino',
                ])
                ->required(),

                TextInput::make('lugar')
                ->label('Lugar de nacimiento')
                ->required()
                ->maxLength(255)
                ,
                
                Section::make('Datos de representantes')
                ->relationship('representante') 
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombres')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('apellidos')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('nacionalidad')
                        ->options([
                            'Venezolana' => 'Venezolana',
                            'Extranjera' => 'Extranjera',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('cedula')
                        ->label('Cédula de Identidad')
                        ->required(),
                ]),
        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                ->label('Nombre')
                ->sortable()
                ->searchable(),
                TextColumn::make('apellidos')->searchable(),
                TextColumn::make('nacionalidad'),
                TextColumn::make('cedula')
                ->label('Cédula de identidad')
                ->sortable()
                ->searchable(),
                TextColumn::make('nacimiento')
                ->label('Fecha de nacimiento'),
                TextColumn::make('genero')
                ->label('Sexo'),
                TextColumn::make('lugar')
                ->label('Lugar de nacimiento'),
            ])
            ->filters([
                 TrashedFilter::make()
                 ->label('Registros inhabilitados')
                 ->truelabel('Solo registros inhabilitados')
                 ->falselabel('Ocultar inhabilitados')
                 ->placeholder('Todos los registros')
                 ,
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->label('Inhabilitar seleccionados')
                    ->modalHeading('¿Inhabilitar estos registros?')
                    ->modalDescription('¿Estas seguro de inhabilitar estos registros?')
                    ->modalSubmitActionLabel("Inhabilitar")
                    ->color("warning")
                    ,
                    
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            //RepresentantesRelationManager::class,//
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstudiantes::route('/'),
            'create' => Pages\CreateEstudiantes::route('/create'),
            'edit' => Pages\EditEstudiantes::route('/{record}/edit'),
        ];
    }
}
