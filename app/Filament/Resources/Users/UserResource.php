<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Pengguna';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengguna')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('roles.name')
                            ->label('Peran')
                            ->badge(),
                        TextEntry::make('wallets_count')
                            ->label('Total Rekening')
                            ->counts('wallets'),
                        ImageEntry::make('avatar')
                            ->label('Avatar')
                            ->circular()
                            ->hidden(fn ($record) => blank($record->avatar)),
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (static::isPengguna()) {
            $query->whereKey(auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        $query = parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (static::isPengguna()) {
            $query->whereKey(auth()->id());
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        if (static::isPengguna()) {
            return auth()->check();
        }

        return parent::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        if (static::isPengguna()) {
            return (int) $record->getKey() === (int) auth()->id();
        }

        return parent::canView($record);
    }

    public static function canEdit(Model $record): bool
    {
        if (static::isPengguna()) {
            return (int) $record->getKey() === (int) auth()->id();
        }

        return parent::canEdit($record);
    }

    public static function canCreate(): bool
    {
        if (static::isPengguna()) {
            return false;
        }

        return parent::canCreate();
    }

    public static function canDelete(Model $record): bool
    {
        if (static::isPengguna()) {
            return false;
        }

        return parent::canDelete($record);
    }

    public static function getNavigationUrl(): string
    {
        if (static::isPengguna() && auth()->check()) {
            return static::getUrl('view', ['record' => auth()->id()]);
        }

        return parent::getNavigationUrl();
    }

    protected static function isPengguna(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->isPengguna() ?? false;
    }
}
