<?php

namespace App\Filament\Resources\Transactions;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionScopeService;
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

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    protected static ?string $recordTitleAttribute = 'transaction_no';

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Transaksi')
                    ->schema([
                        TextEntry::make('transaction_no')
                            ->label('Nomor Transaksi'),
                        TextEntry::make('transaction_type')
                            ->label('Tipe Transaksi')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => filled($state) ? TransactionTypeEnum::from($state)->label() : '-'),
                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->date(),
                        TextEntry::make('wallet.display_name')
                            ->label('Rekening'),
                        TextEntry::make('amount')
                            ->label('Nominal')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),
                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->default('-'),
                        TextEntry::make('goal.name')
                            ->label('Target Tabungan')
                            ->default('-'),
                        ImageEntry::make('image')
                            ->label('Bukti Gambar')
                            ->disk('public')
                            ->visibility('public')
                            ->hidden(fn ($record) => blank($record->image)),
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
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = app(TransactionScopeService::class)->scopeTransactionQuery(
            parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]),
        );

        if (! static::isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'view' => ViewTransaction::route('/{record}'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        $query = app(TransactionScopeService::class)->scopeTransactionQuery(
            parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]),
        );

        if (! static::isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    public static function canEdit(Model $record): bool
    {
        return static::isSuperAdmin() || ((int) $record->created_by === (int) auth()->id());
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function canRestore(Model $record): bool
    {
        return static::canEdit($record);
    }

    protected static function isSuperAdmin(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return $user?->isSuperAdmin() ?? false;
    }
}
