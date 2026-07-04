<?php

namespace App\Filament\Resources\TransactionLedgers\Tables;

use App\Models\Wallet;
use App\Services\TransactionScopeService;
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
                    ->label('Tipe Transaksi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst((string) $state) : '-')
                    ->toggleable(),
                TextColumn::make('ref_type')
                    ->label('Ref Type')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst((string) $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_amount')
                    ->label('Saldo Sebelumnya')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('end_amount')
                    ->label('Saldo Akhir')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('wallet.bank_name')
                    ->label('Wallet')
                    ->description(fn($record) => $record->wallet->account_name)
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->toggleable(),
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
                SelectFilter::make('wallet_id')
                    ->label('Wallet')
                    ->options(fn (): array => app(TransactionScopeService::class)
                        ->scopeWalletQuery(Wallet::query())
                        ->get()
                        ->mapWithKeys(fn (Wallet $wallet): array => [
                            $wallet->id => $wallet->display_name,
                        ])
                        ->all())
                    ->searchable()
                    ->preload(),
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
