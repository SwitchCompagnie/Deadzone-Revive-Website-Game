<?php

namespace App\Filament\Resources\AdminAuditLogs\Pages;

use App\Filament\Resources\AdminAuditLogs\AdminAuditLogResource;
use Filament\Infolists\Components as InfolistComponents;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;

class ViewAdminAuditLog extends ViewRecord
{
    protected static string $resource = AdminAuditLogResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Informations générales')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                InfolistComponents\TextEntry::make('created_at')
                                    ->label('Date & Heure')
                                    ->dateTime('d/m/Y H:i:s'),

                                InfolistComponents\TextEntry::make('user.name')
                                    ->label('Utilisateur')
                                    ->default(fn ($record) => $record->user_name ?? 'Système')
                                    ->url(fn ($record) => $record->user_id ? route('filament.admin.resources.users.edit', ['record' => $record->user_id]) : null),

                                InfolistComponents\TextEntry::make('user.email')
                                    ->label('Email utilisateur')
                                    ->default('N/A'),

                                InfolistComponents\TextEntry::make('action')
                                    ->label('Action')
                                    ->formatStateUsing(fn ($record) => $record->action_label)
                                    ->badge()
                                    ->color(fn ($record) => $record->action_color),
                            ]),
                    ]),

                Components\Section::make('Ressource concernée')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                InfolistComponents\TextEntry::make('resource_name')
                                    ->label('Type de ressource'),

                                InfolistComponents\TextEntry::make('resource_title')
                                    ->label('Élément'),

                                InfolistComponents\TextEntry::make('resource_id')
                                    ->label('ID de l\'enregistrement'),

                                InfolistComponents\TextEntry::make('resource_type')
                                    ->label('Classe du modèle')
                                    ->default('N/A'),
                            ]),

                        InfolistComponents\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Anciennes valeurs')
                    ->schema([
                        InfolistComponents\KeyValueEntry::make('old_values')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->old_values))
                    ->collapsible(),

                Components\Section::make('Nouvelles valeurs')
                    ->schema([
                        InfolistComponents\KeyValueEntry::make('new_values')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->new_values))
                    ->collapsible(),

                Components\Section::make('Informations techniques')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                InfolistComponents\TextEntry::make('ip_address')
                                    ->label('Adresse IP'),

                                InfolistComponents\TextEntry::make('url')
                                    ->label('URL')
                                    ->limit(50)
                                    ->tooltip(fn ($record) => $record->url),

                                InfolistComponents\TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->columnSpanFull()
                                    ->limit(100)
                                    ->tooltip(fn ($record) => $record->user_agent),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Components\Section::make('Métadonnées')
                    ->schema([
                        InfolistComponents\KeyValueEntry::make('metadata')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->metadata))
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
