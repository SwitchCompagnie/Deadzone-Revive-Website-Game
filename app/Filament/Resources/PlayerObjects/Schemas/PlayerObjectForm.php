<?php

namespace App\Filament\Resources\PlayerObjects\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PlayerObjectForm
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
