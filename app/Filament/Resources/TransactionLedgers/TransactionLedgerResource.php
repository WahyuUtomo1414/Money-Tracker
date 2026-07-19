<?php

namespace App\Filament\Resources\TransactionLedgers;

use App\Filament\Resources\TransactionLedgers\Pages\ListTransactionLedgers;
use App\Filament\Resources\TransactionLedgers\Tables\TransactionLedgersTable;
use App\Models\TransactionLedger;
use App\Services\TransactionScopeService;
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

    protected static ?string $navigationLabel = 'Riwayat Transaksi';

    protected static ?string $modelLabel = 'Riwayat Transaksi';

    protected static ?string $pluralModelLabel = 'Riwayat Transaksi';

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
        return app(TransactionScopeService::class)->scopeTransactionLedgerQuery(
            parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]),
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionLedgers::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return app(TransactionScopeService::class)->scopeTransactionLedgerQuery(
            parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]),
        );
    }
}
