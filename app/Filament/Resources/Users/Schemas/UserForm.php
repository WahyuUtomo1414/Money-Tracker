<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(128),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Terverifikasi Pada'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        Select::make('roles')
                            ->label('Peran')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false),
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('avatars'),
                        Toggle::make('active')
                            ->label('Aktif')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
