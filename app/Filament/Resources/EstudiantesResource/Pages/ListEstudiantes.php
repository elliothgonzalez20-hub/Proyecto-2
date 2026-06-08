<?php

namespace App\Filament\Resources\EstudiantesResource\Pages;

use App\Filament\Resources\EstudiantesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstudiantes extends ListRecords
{
    protected static string $resource = EstudiantesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
