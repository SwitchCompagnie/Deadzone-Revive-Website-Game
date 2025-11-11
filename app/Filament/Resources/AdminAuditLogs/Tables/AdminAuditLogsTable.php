<?php

namespace App\Filament\Resources\AdminAuditLogs\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminAuditLogsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Heure')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable()
                    ->default(fn ($record) => $record->user_name ?? 'Système')
                    ->description(fn ($record) => $record->user?->email),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Action')
                    ->formatStateUsing(fn ($record) => $record->action_label)
                    ->color(fn ($record) => $record->action_color)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('resource_name')
                    ->label('Ressource')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('resource_title')
                    ->label('Élément')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Adresse IP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'view' => 'Consultation',
                        'create' => 'Création',
                        'update' => 'Modification',
                        'delete' => 'Suppression',
                        'restore' => 'Restauration',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('resource_name')
                    ->label('Ressource')
                    ->options(function () {
                        return \App\Models\AdminAuditLog::query()
                            ->whereNotNull('resource_name')
                            ->distinct()
                            ->pluck('resource_name', 'resource_name')
                            ->toArray();
                    })
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Du'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label('Détails'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Rafraîchir automatiquement toutes les 30 secondes
            ->striped();
    }
}
