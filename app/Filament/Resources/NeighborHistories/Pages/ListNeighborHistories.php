<?php

namespace App\Filament\Resources\NeighborHistories\Pages;

use App\Filament\Resources\NeighborHistories\NeighborHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNeighborHistories extends ListRecords
{
    protected static string $resource = NeighborHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
