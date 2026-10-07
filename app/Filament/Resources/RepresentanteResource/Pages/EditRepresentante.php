<?php

namespace App\Filament\Resources\RepresentanteResource\Pages;

use App\Filament\Resources\RepresentanteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepresentante extends EditRecord
{
    protected static string $resource = RepresentanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
