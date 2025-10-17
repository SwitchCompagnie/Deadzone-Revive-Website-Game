<?php

namespace App\Filament\Resources\NeighborHistories\Pages;

use App\Filament\Resources\NeighborHistories\NeighborHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNeighborHistory extends EditRecord
{
    protected static string $resource = NeighborHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
