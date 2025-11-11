<?php

namespace App\Filament\Resources\AdminAuditLogs;

use App\Filament\Resources\AdminAuditLogs\Pages\ListAdminAuditLogs;
use App\Filament\Resources\AdminAuditLogs\Pages\ViewAdminAuditLog;
use App\Filament\Resources\AdminAuditLogs\Tables\AdminAuditLogsTable;
use App\Models\AdminAuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AdminAuditLogResource extends Resource
{
    protected static ?string $model = AdminAuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Traces d\'audit';

    protected static ?string $modelLabel = 'Trace d\'audit';

    protected static ?string $pluralModelLabel = 'Traces d\'audit';

    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        // Les logs d'audit ne sont pas modifiables
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return AdminAuditLogsTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminAuditLogs::route('/'),
            'view' => ViewAdminAuditLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Les logs d'audit ne peuvent pas être créés manuellement
    }

    public static function canEdit($record): bool
    {
        return false; // Les logs d'audit ne peuvent pas être modifiés
    }

    public static function canDelete($record): bool
    {
        return false; // Les logs d'audit ne peuvent pas être supprimés
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
