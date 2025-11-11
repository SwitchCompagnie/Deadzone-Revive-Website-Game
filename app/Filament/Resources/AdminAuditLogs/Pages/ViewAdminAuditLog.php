<?php

namespace App\Filament\Resources\AdminAuditLogs\Pages;

use App\Filament\Resources\AdminAuditLogs\AdminAuditLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewAdminAuditLog extends ViewRecord
{
    protected static string $resource = AdminAuditLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informations générales')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('Date & Heure')
                                    ->dateTime('d/m/Y H:i:s'),

                                Components\TextEntry::make('user.name')
                                    ->label('Utilisateur')
                                    ->default(fn ($record) => $record->user_name ?? 'Système')
                                    ->url(fn ($record) => $record->user_id ? route('filament.admin.resources.users.edit', ['record' => $record->user_id]) : null),

                                Components\TextEntry::make('user.email')
                                    ->label('Email utilisateur')
                                    ->default('N/A'),

                                Components\TextEntry::make('action')
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
                                Components\TextEntry::make('resource_name')
                                    ->label('Type de ressource'),

                                Components\TextEntry::make('resource_title')
                                    ->label('Élément'),

                                Components\TextEntry::make('resource_id')
                                    ->label('ID de l\'enregistrement'),

                                Components\TextEntry::make('resource_type')
                                    ->label('Classe du modèle')
                                    ->default('N/A'),
                            ]),

                        Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Anciennes valeurs')
                    ->schema([
                        Components\KeyValueEntry::make('old_values')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->old_values))
                    ->collapsible(),

                Components\Section::make('Nouvelles valeurs')
                    ->schema([
                        Components\KeyValueEntry::make('new_values')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->new_values))
                    ->collapsible(),

                Components\Section::make('Informations techniques')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('ip_address')
                                    ->label('Adresse IP'),

                                Components\TextEntry::make('url')
                                    ->label('URL')
                                    ->limit(50)
                                    ->tooltip(fn ($record) => $record->url),

                                Components\TextEntry::make('user_agent')
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
                        Components\KeyValueEntry::make('metadata')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->metadata))
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
