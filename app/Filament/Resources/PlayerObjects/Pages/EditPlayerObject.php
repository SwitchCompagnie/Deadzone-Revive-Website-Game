<?php

namespace App\Filament\Resources\PlayerObjects\Pages;

use App\Filament\Resources\PlayerObjects\PlayerObjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlayerObject extends EditRecord
{
    protected static string $resource = PlayerObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
