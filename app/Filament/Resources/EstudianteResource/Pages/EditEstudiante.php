<?php

namespace App\Filament\Resources\EstudianteResource\Pages;

use App\Filament\Resources\EstudianteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstudiante extends EditRecord
{
    protected static string $resource = EstudianteResource::class;

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
