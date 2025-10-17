<?php

namespace App\Filament\Resources\PlayerObjects\Pages;

use App\Filament\Resources\PlayerObjects\PlayerObjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlayerObjects extends ListRecords
{
    protected static string $resource = PlayerObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
