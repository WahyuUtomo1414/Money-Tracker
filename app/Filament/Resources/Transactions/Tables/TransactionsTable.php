<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionsTable
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
                TextColumn::make('transaction_type')
                    ->label('Tipe Transaksi')
                    ->badge()
                    ->searchable(),
                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
                ImageColumn::make('image')
                    ->label('Bukti'),
                TextColumn::make('wallet.account_name')
                    ->label('Wallet')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('goal.name')
                    ->label('Target Tabungan')
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
                SelectFilter::make('transaction_type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'topup' => 'Topup',
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'adjustment' => 'Adjustment',
                    ]),
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
