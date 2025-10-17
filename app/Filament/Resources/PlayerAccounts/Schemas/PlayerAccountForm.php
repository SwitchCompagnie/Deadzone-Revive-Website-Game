<?php

namespace App\Filament\Resources\PlayerAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PlayerAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('hashed_password')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('display_name')
                    ->required(),
                TextInput::make('avatar_url')
                    ->url()
                    ->required(),
                TextInput::make('last_login')
                    ->required()
                    ->numeric(),
                TextInput::make('country_code')
                    ->default(null),
                Textarea::make('server_metadata_json')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
