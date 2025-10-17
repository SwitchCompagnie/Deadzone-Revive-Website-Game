<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('data_json')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
