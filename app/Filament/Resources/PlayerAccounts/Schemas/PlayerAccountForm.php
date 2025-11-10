<?php

namespace App\Filament\Resources\PlayerAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;

class PlayerAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->schema([
                        TextInput::make('player_id')
                            ->label('Player ID')
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->maxLength(36),
                        Textarea::make('hashed_password')
                            ->label('Hashed Password')
                            ->required()
                            ->columnSpanFull()
                            ->rows(3),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('display_name')
                            ->label('Display Name')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('avatar_url')
                            ->label('Avatar URL')
                            ->url()
                            ->required()
                            ->maxLength(500),
                        TextInput::make('last_login')
                            ->label('Last Login (timestamp)')
                            ->required()
                            ->numeric(),
                        TextInput::make('country_code')
                            ->label('Country Code')
                            ->maxLength(10)
                            ->default(null),
                    ])
                    ->columns(2),

                Section::make('Server Metadata')
                    ->schema([
                        Textarea::make('server_metadata_json')
                            ->label('Server Metadata (JSON)')
                            ->required()
                            ->columnSpanFull()
                            ->rows(10)
                            ->helperText('JSON format: contains notes, flags, and extra metadata'),
                    ])
                    ->collapsible(),

                Section::make('Player Objects')
                    ->schema([
                        Textarea::make('player_objects_json')
                            ->label('Player Objects (JSON)')
                            ->required()
                            ->columnSpanFull()
                            ->rows(15)
                            ->helperText('JSON format: contains player data, survivors, buildings, resources, etc.'),
                    ])
                    ->collapsible(),

                Section::make('Neighbor History')
                    ->schema([
                        Textarea::make('neighbor_history_json')
                            ->label('Neighbor History (JSON)')
                            ->required()
                            ->columnSpanFull()
                            ->rows(10)
                            ->helperText('JSON format: contains neighbor map data'),
                    ])
                    ->collapsible(),

                Section::make('Inventory')
                    ->schema([
                        Textarea::make('inventory_json')
                            ->label('Inventory (JSON)')
                            ->required()
                            ->columnSpanFull()
                            ->rows(15)
                            ->helperText('JSON format: contains inventory items and schematics'),
                    ])
                    ->collapsible(),
            ]);
    }
}
