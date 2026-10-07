<?php

namespace App\Filament\Resources\RepresentanteResource\Pages;

use App\Filament\Resources\RepresentanteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepresentantes extends ListRecords
{
    protected static string $resource = RepresentanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
