<?php

namespace App\Filament\Resources\NeighborHistories;

use App\Filament\Resources\NeighborHistories\Pages\CreateNeighborHistory;
use App\Filament\Resources\NeighborHistories\Pages\EditNeighborHistory;
use App\Filament\Resources\NeighborHistories\Pages\ListNeighborHistories;
use App\Filament\Resources\NeighborHistories\Schemas\NeighborHistoryForm;
use App\Filament\Resources\NeighborHistories\Tables\NeighborHistoriesTable;
use App\Models\NeighborHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NeighborHistoryResource extends Resource
{
    protected static ?string $model = NeighborHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return NeighborHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NeighborHistoriesTable::configure($table);
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
            'index' => ListNeighborHistories::route('/'),
            'create' => CreateNeighborHistory::route('/create'),
            'edit' => EditNeighborHistory::route('/{record}/edit'),
        ];
    }
}
