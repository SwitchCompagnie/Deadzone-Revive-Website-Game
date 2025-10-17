<?php

namespace App\Filament\Resources\NeighborHistories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class NeighborHistoryForm
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
