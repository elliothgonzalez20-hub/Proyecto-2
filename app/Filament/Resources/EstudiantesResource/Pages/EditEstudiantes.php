<?php

namespace App\Filament\Resources\EstudiantesResource\Pages;

use App\Filament\Resources\EstudiantesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstudiantes extends EditRecord
{
    protected static string $resource = EstudiantesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->label("Inhabilitar")
            ->modalHeading("¿Inhabilitar estudiante?")
            ->modalDescription("¿Estas seguro de que deseas inhabilitar este registro?")
            ->modalSubmitActionLabel("Inhabilitar")
            ->successNotificationTitle("Registro inhabilitado con éxito")
            ->color("warning")
            ->icon("heroicon-o-x-circle")
            ,
        ];
    }
}
