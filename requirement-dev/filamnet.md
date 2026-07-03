# Requirement Filament Resource

## 1. Tujuan

Dokumen ini menjadi acuan pembuatan seluruh resource admin menggunakan **Filament** untuk project Money Tracker.

Scope tahap ini:

- generate resource memakai command artisan,
- fokus pada **resource, form, dan table**,
- default **tidak** memakai `view page`,
- default **tidak** memakai `infolist`,
- resource mengikuti pola struktur Filament yang modular dan rapi.

Catatan:

- Project ini dipakai untuk kebutuhan pribadi, jadi Filament akan menjadi panel admin utama untuk mengelola data keuangan.
- Resource difokuskan untuk master data dan transaksi inti.

## 2. Model Yang Akan Dibuatkan Resource

Model domain yang saat ini tersedia:

- `User`
- `Category`
- `Wallet`
- `Goal`
- `Transaction`
- `TransactionLedger`

Model yang **tidak masuk** batch resource utama:

- `UserWallet`

Catatan:

- `TransactionLedger` tetap dibuatkan resource, tetapi hanya dipakai untuk `table` atau readonly list page.
- `TransactionLedger` tidak memiliki form create atau edit manual.
- `UserWallet` adalah tabel pivot atau mapping many-to-many, jadi tidak perlu dibuat resource terpisah.
- Relasi user dengan wallet akan dikelola dari resource `User` atau `Wallet`.

## 3. Command Generate Resource

Semua command di bawah mengikuti preferensi:

- pakai `php artisan make:filament-resource`
- pakai `--generate`
- pakai `--soft-deletes`

Daftar command:

```bash
php artisan make:filament-resource User --generate --soft-deletes
php artisan make:filament-resource Category --generate --soft-deletes
php artisan make:filament-resource Wallet --generate --soft-deletes
php artisan make:filament-resource Goal --generate --soft-deletes
php artisan make:filament-resource Transaction --generate --soft-deletes
php artisan make:filament-resource TransactionLedger --generate --soft-deletes
```

## 4. Standar Struktur Resource

Setelah command dijalankan, setiap resource dirapikan mengikuti pola seperti ini:

```php
<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
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
```

## 5. Properti Yang Wajib Ada di Semua Resource

Properti ini dipakai di semua resource:

```php
protected static ?string $model = ModelName::class;

protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

protected static string|UnitEnum|null $navigationGroup = 'Nama Group';

protected static ?string $navigationLabel = 'Label';

protected static ?string $modelLabel = 'Label';

protected static ?string $pluralModelLabel = 'Label';
```

## 6. Standar Page Yang Dipakai

Page yang dipakai:

- `List`
- `Create`
- `Edit`

Pengecualian:

- `TransactionLedger` hanya memakai `List`

Page yang **tidak dipakai**:

- `View`

Komponen yang **tidak dipakai**:

- `Infolist`

Artinya setelah generate, file berikut nanti akan dibuang atau tidak dipakai:

- `Pages/View...`
- `Schemas/...Infolist.php`
- method `infolist()`
- route `'view' => ...`

Khusus `TransactionLedger`:

- tidak memakai `Create`
- tidak memakai `Edit`
- tidak memakai `form schema`
- hanya menampilkan daftar histori ledger

## 7. Rekomendasi Navigation Group

Supaya sidebar admin rapi, resource bisa dikelompokkan seperti ini:

### 7.1 Pengguna

- `User`

### 7.2 Master Data

- `Category`
- `Wallet`
- `Goal`

### 7.3 Transaksi

- `Transaction`
- `TransactionLedger`

## 8. Rekomendasi Label Per Resource

| Model | Navigation Label | Model Label | Plural Model Label | Navigation Group |
|---|---|---|---|---|
| `User` | `Pengguna` | `Pengguna` | `Pengguna` | `Pengguna` |
| `Category` | `Kategori` | `Kategori` | `Kategori` | `Master Data` |
| `Wallet` | `Wallet` | `Wallet` | `Wallet` | `Master Data` |
| `Goal` | `Target Tabungan` | `Target Tabungan` | `Target Tabungan` | `Master Data` |
| `Transaction` | `Transaksi` | `Transaksi` | `Transaksi` | `Transaksi` |
| `TransactionLedger` | `Ledger Transaksi` | `Ledger Transaksi` | `Ledger Transaksi` | `Transaksi` |

## 9. Standar Implementasi Tahap Berikutnya

Setelah command di atas dijalankan, penyesuaian yang akan dilakukan:

- rapikan resource class agar sesuai pola Filament,
- hapus `view` page dan `infolist`,
- sesuaikan `navigationGroup`, `navigationLabel`, `modelLabel`, dan `pluralModelLabel`,
- rapikan `form schema`,
- rapikan `table schema`,
- tambahkan kolom audit `createdBy`, `updatedBy`, dan `deletedBy` di semua table resource,
- aktifkan query tanpa `SoftDeletingScope`,
- tetap pertahankan dukungan soft delete pada table action dan filter.

Catatan khusus:

- `TransactionLedgerResource` hanya dirapikan pada `table schema`.
- `TransactionLedgerResource` tidak menyediakan input manual karena data ledger berasal dari proses sistem.

## 10. Standar Kolom Audit di Table

Semua table resource wajib menambahkan kolom berikut:

```php
TextColumn::make('createdBy.name')
    ->label('Dibuat Oleh')
    ->badge()
    ->description(fn ($record) => $record->created_at?->format('d M Y H:i'))
    ->sortable(),

TextColumn::make('updatedBy.name')
    ->label('Diubah Oleh')
    ->badge()
    ->description(fn ($record) => $record->updated_at?->format('d M Y H:i'))
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true),

TextColumn::make('deletedBy.name')
    ->label('Dihapus Oleh')
    ->badge()
    ->description(fn ($record) => $record->deleted_at?->format('d M Y H:i'))
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true),
```

Catatan:

- `Dibuat Oleh` tampil default di table.
- `Diubah Oleh` dan `Dihapus Oleh` bisa dibuat toggleable agar table tetap ringkas.

## 11. Rekomendasi Form Per Resource

### 11.1 User

Field utama:

- `name`
- `email`
- `password`
- `avatar`
- `active`

Catatan:

- password hanya wajib saat create.
- password saat edit bersifat opsional.
- avatar dapat memakai upload image.

### 11.2 Category

Field utama:

- `name`
- `type`
- `description`
- `active`

Catatan:

- `type` merepresentasikan konteks kategori, misalnya `wallet` atau `transaction`.

### 11.3 Wallet

Field utama:

- `category_id`
- `bank_name`
- `account_no`
- `account_name`
- `description`
- `active`

Catatan:

- `category_id` memakai select relasi ke `Category`.

### 11.4 Goal

Field utama:

- `wallet_id`
- `name`
- `description`
- `target_amount`
- `target_date`
- `active`

Catatan:

- `wallet_id` memakai select relasi ke `Wallet`.
- `target_amount` memakai komponen numeric atau money input.

### 11.5 Transaction

Field utama:

- `uuid`
- `transaction_no`
- `transaction_type`
- `transaction_date`
- `wallet_id`
- `category_id`
- `goal_id`
- `amount`
- `description`
- `image`
- `active`

Catatan:

- `uuid` tidak perlu diinput manual.
- `transaction_no` readonly, diisi otomatis dari service.
- `transaction_type` wajib mengikuti enum `topup`, `payment`, `refund`, `adjustment`.
- `wallet_id`, `category_id`, dan `goal_id` memakai select relasi.
- `image` memakai upload file atau image upload.

### 11.6 TransactionLedger

Field form:

- tidak ada

Catatan:

- resource ini tidak menyediakan create dan edit.
- ledger hanya ditampilkan sebagai histori readonly.

## 12. Rekomendasi Table Per Resource

### 12.1 User

Kolom utama:

- nama
- email
- active
- createdBy
- updatedBy

### 12.2 Category

Kolom utama:

- name
- type
- active
- createdBy

### 12.3 Wallet

Kolom utama:

- bank_name
- account_no
- account_name
- category.name
- active

### 12.4 Goal

Kolom utama:

- name
- wallet.account_name
- target_amount
- target_date
- active

### 12.5 Transaction

Kolom utama:

- transaction_no
- transaction_type
- transaction_date
- wallet.account_name
- category.name
- goal.name
- amount
- active

Catatan:

- `goal` dan `category` sebaiknya toggleable karena tidak semua transaksi pasti memiliki nilai.

### 12.6 TransactionLedger

Kolom utama:

- transaction_no
- transaction_date
- wallet.account_name
- category.name
- amount
- last_amount
- end_amount
- ref_type
- createdBy

Catatan:

- table ini fokus untuk audit perubahan saldo.
- `category` bisa dibuat toggleable karena opsional.
- `ref_type` dan `ref_id` berguna untuk tracing sumber data.

## 13. Catatan Khusus Repo Ini

- Model saat ini sudah memakai `AuditedBySoftDelete` dan `SoftDeletes` pada entitas utama, jadi Filament bisa langsung memanfaatkan relasi audit.
- Nama tabel saat ini mengikuti keputusan repo: `users` dan `goals` tetap plural, sedangkan tabel domain lain diarahkan ke nama singular seperti `category`, `wallet`, `transaction`, dan `transaction_ledger`.
- Resource `TransactionLedger` tetap dibuat, tetapi hanya sebagai readonly table resource.
- Resource `UserWallet` tidak perlu dibuat karena hanya pivot relasi.

## 14. Urutan Pengerjaan Yang Direkomendasikan

Urutan implementasi resource yang disarankan:

1. `CategoryResource`
2. `WalletResource`
3. `GoalResource`
4. `TransactionResource`
5. `TransactionLedgerResource`
6. `UserResource`

Alasan:

- master data harus siap lebih dulu,
- transaksi bergantung pada wallet, category, dan goal,
- user resource bisa dirapikan setelah relasi wallet final.
