<?php

namespace App\Filament\Resources\TransactionLedgers\Tables;

use App\Models\Transaction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionLedgersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('transaction_no')
                    ->label('Nomor Transaksi')
                    ->searchable(),
                TextColumn::make('reference.transaction_type')
                    ->label('Tipe Transaksi')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $record->reference instanceof Transaction ? ucfirst($record->reference->transaction_type) : '-')
                    ->toggleable(),
                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('ref_id')
                    ->label('Ref ID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('ref_type')
                    ->label('Ref Type')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('last_amount')
                    ->label('Saldo Sebelumnya')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('end_amount')
                    ->label('Saldo Akhir')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('wallet.account_name')
                    ->label('Wallet')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->badge()
                    ->description(fn ($record) => $record->created_at?->format('d M Y H:i'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('updated_by')
                    ->label('Diubah Oleh')
                    ->relationship('updatedBy', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('deleted_by')
                    ->label('Dihapus Oleh')
                    ->relationship('deletedBy', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
