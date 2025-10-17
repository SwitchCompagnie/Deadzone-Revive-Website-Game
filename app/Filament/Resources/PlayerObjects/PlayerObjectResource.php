<?php

namespace App\Filament\Resources\PlayerObjects;

use App\Filament\Resources\PlayerObjects\Pages\CreatePlayerObject;
use App\Filament\Resources\PlayerObjects\Pages\EditPlayerObject;
use App\Filament\Resources\PlayerObjects\Pages\ListPlayerObjects;
use App\Filament\Resources\PlayerObjects\Schemas\PlayerObjectForm;
use App\Filament\Resources\PlayerObjects\Tables\PlayerObjectsTable;
use App\Models\PlayerObject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlayerObjectResource extends Resource
{
    protected static ?string $model = PlayerObject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PlayerObjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlayerObjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayerObjects::route('/'),
            'create' => CreatePlayerObject::route('/create'),
            'edit' => EditPlayerObject::route('/{record}/edit'),
        ];
    }
}
