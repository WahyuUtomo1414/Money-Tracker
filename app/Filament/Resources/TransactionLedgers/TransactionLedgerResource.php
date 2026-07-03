<?php

namespace App\Filament\Resources\TransactionLedgers;

use App\Filament\Resources\TransactionLedgers\Pages\ListTransactionLedgers;
use App\Filament\Resources\TransactionLedgers\Tables\TransactionLedgersTable;
use App\Models\TransactionLedger;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TransactionLedgerResource extends Resource
{
    protected static ?string $model = TransactionLedger::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Ledger Transaksi';

    protected static ?string $modelLabel = 'Ledger Transaksi';

    protected static ?string $pluralModelLabel = 'Ledger Transaksi';

    protected static ?string $recordTitleAttribute = 'transaction_no';

    public static function table(Table $table): Table
    {
        return TransactionLedgersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
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
            'index' => ListTransactionLedgers::route('/'),
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
