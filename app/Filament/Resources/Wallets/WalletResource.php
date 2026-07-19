<?php

namespace App\Filament\Resources\Wallets;

use App\Filament\Resources\Wallets\Pages\CreateWallet;
use App\Filament\Resources\Wallets\Pages\EditWallet;
use App\Filament\Resources\Wallets\Pages\ListWallets;
use App\Filament\Resources\Wallets\Pages\ViewWallet;
use App\Filament\Resources\Wallets\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Wallets\Schemas\WalletForm;
use App\Filament\Resources\Wallets\Tables\WalletsTable;
use App\Models\Wallet;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Rekening';

    protected static ?string $modelLabel = 'Rekening';

    protected static ?string $pluralModelLabel = 'Rekening';

    protected static ?string $recordTitleAttribute = 'account_name';

    public static function form(Schema $schema): Schema
    {
        return WalletForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalletsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Rekening')
                    ->schema([
                        TextEntry::make('display_name')
                            ->label('Rekening'),
                        TextEntry::make('category.name')
                            ->label('Kategori'),
                        TextEntry::make('account_no')
                            ->label('Nomor Rekening'),
                        IconEntry::make('active')
                            ->label('Aktif')
                            ->boolean(),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->default('-'),
                        TextEntry::make('createdBy.name')
                            ->label('Dibuat Oleh')
                            ->badge()
                            ->default('-'),
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
            UsersRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWallets::route('/'),
            'create' => CreateWallet::route('/create'),
            'view' => ViewWallet::route('/{record}'),
            'edit' => EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
