<?php

namespace App\Filament\Resources\PlayerAccounts\Pages;

use App\Filament\Resources\PlayerAccounts\PlayerAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlayerAccount extends EditRecord
{
    protected static string $resource = PlayerAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
