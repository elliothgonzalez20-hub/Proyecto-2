<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstudiantesResource\Pages;
use App\Filament\Resources\EstudiantesResource\RelationManagers;
use App\Models\Estudiantes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms\Components\Select;
use Ramsey\Uuid\Type\Integer;


class EstudiantesResource extends Resource
{
    protected static ?string $model = Estudiantes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            
                 TextInput::make('name')
                 ->required()
                 ->maxLength('255'),
        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListEstudiantes::route('/'),
            'create' => Pages\CreateEstudiantes::route('/create'),
            'edit' => Pages\EditEstudiantes::route('/{record}/edit'),
        ];
    }
}
