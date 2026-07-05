# Arsitektur Money Tracker

## 1. Ringkasan Teknis

Project akan dibangun dengan:

- PHP 8.3
- Laravel 13
- Filament sebagai admin panel utama
- MySQL atau MariaDB sebagai database relasional

Arsitektur diarahkan ke pola Laravel standard yang diperjelas dengan pemisahan:

- enum untuk konstanta bisnis,
- service untuk logic generate nomor transaksi,
- model untuk relasi data,
- Filament resource untuk panel CRUD.

## 2. Prinsip Arsitektur

- Mengikuti konvensi default Laravel agar maintainable.
- Business rule penting tidak ditaruh langsung di controller atau resource.
- Enum menjadi source of truth untuk nilai tetap seperti `transaction_type`.
- Service terpisah dipakai untuk proses yang reusable seperti generate `transaction_no`.
- Ledger dibuat melalui flow aplikasi, bukan dari input manual biasa.

## 3. Struktur Folder yang Direkomendasikan

```text
app/
├── Enums/
│   ├── TransactionTypeEnum.php
│   └── CategoryTypeEnum.php
├── Filament/
│   ├── Resources/
│   │   ├── CategoryResource.php
│   │   ├── WalletResource.php
│   │   ├── GoalResource.php
│   │   ├── TransactionResource.php
│   │   └── UserResource.php
│   ├── Resources/CategoryResource/Pages/
│   ├── Resources/WalletResource/Pages/
│   ├── Resources/GoalResource/Pages/
│   ├── Resources/TransactionResource/Pages/
│   └── Widgets/
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Wallet.php
│   ├── Goal.php
│   ├── Transaction.php
│   ├── TransactionLedger.php
│   └── UserWallet.php
├── Services/
│   ├── TransactionNumberService.php
│   ├── TransactionPostingService.php
│   └── WalletBalanceService.php
├── Actions/
│   └── Transactions/
│       └── CreateTransactionAction.php
├── Data/
│   └── TransactionData.php
├── Observers/
│   └── TransactionObserver.php
└── Providers/
```

## 4. Penjelasan Per Layer

### 4.1 Enums

Folder `app/Enums` dipakai untuk nilai bisnis yang tetap.

Contoh `TransactionTypeEnum`:

- `topup`
- `payment`
- `refund`
- `adjustment`

Tiap enum juga menyimpan prefix nomor transaksi.

Contoh method yang direkomendasikan:

- `label(): string`
- `prefix(): string`
- `isCredit(): bool`
- `isDebit(): bool`

Dengan cara ini, seluruh aturan dasar tipe transaksi ada di satu tempat.

### 4.2 Models

Setiap tabel utama memiliki model sendiri.

Relasi utama yang direkomendasikan:

- `User` belongsToMany `Wallet`
- `Wallet` belongsTo `Category`
- `Wallet` belongsToMany `User`
- `Goal` belongsTo `Wallet`
- `Transaction` belongsTo `Wallet`
- `Transaction` belongsTo `Category`
- `Transaction` belongsTo `Goal`
- `TransactionLedger` belongsTo `Wallet`
- `TransactionLedger` morph reference sederhana melalui `ref_id` dan `ref_type`

### 4.3 Services

#### TransactionNumberService

Tanggung jawab:

- Menerima tipe transaksi.
- Mengambil prefix dari enum.
- Menghasilkan nomor transaksi unik.

Contoh flow:

1. Terima `TransactionTypeEnum`.
2. Ambil prefix dari method enum.
3. Susun format nomor transaksi.
4. Cek sequence harian atau sequence global.
5. Kembalikan nilai final.

#### TransactionPostingService

Tanggung jawab:

- Membuat transaksi.
- Menghitung efek saldo berdasarkan tipe transaksi.
- Membuat atau memperbarui ledger secara atomik dalam database transaction.
- Menyimpan hubungan `transaction.id -> transaction_ledger.ref_id`.
- Menghitung ulang saldo ledger setelah titik transaksi yang berubah.

#### TransactionLedgerService

Tanggung jawab:

- `create`: membuat row ledger untuk transaksi lalu menghitung ulang saldo dari transaksi tersebut ke bawah.
- `update`: mencari ledger berdasarkan `ref_id`, mengganti data ledger transaksi tersebut, lalu menghitung ulang saldo transaksi setelahnya.
- `delete`: menghapus ledger berdasarkan `ref_id`, lalu menghitung ulang saldo transaksi setelahnya.
- Recalculation dibatasi pada wallet terkait agar tidak memindai semua transaksi lintas wallet.

## 5. Rekomendasi Struktur Filament

Filament akan dipakai sebagai backoffice utama.

Resource minimal:

- `UserResource`
- `CategoryResource`
- `WalletResource`
- `GoalResource`
- `TransactionResource`

Pada `TransactionResource`, form sebaiknya memiliki field:

- transaction type
- transaction date
- wallet
- category
- goal
- amount
- description
- image

Field `transaction_no` tidak perlu diinput manual, cukup tampil sebagai readonly setelah record dibuat.

## 6. Rekomendasi Alur Create Transaction

```text
Filament Form
-> validasi input
-> map transaction_type ke TransactionTypeEnum
-> panggil TransactionPostingService
-> generate transaction_no via TransactionNumberService
-> simpan transaction
-> simpan transaction_ledger dengan ref_id = transaction.id
-> hitung ulang last_amount dan end_amount mulai dari transaksi tersebut
-> commit database transaction
```

## 7. Konvensi Kode yang Disarankan

- Gunakan singular untuk foreign key: `goal_id`, bukan `goals_id`.
- Gunakan `uuid` untuk identitas publik bila dibutuhkan pada URL atau integrasi.
- Nilai uang sebaiknya dipertimbangkan memakai `decimal`, bukan `double`, untuk akurasi finansial.
- Simpan upload bukti transaksi melalui disk Laravel, lalu simpan path di kolom `image`.

## 8. Rekomendasi Namespace Awal

```php
App\Enums\TransactionTypeEnum
App\Models\Transaction
App\Models\TransactionLedger
App\Services\TransactionNumberService
App\Services\TransactionPostingService
App\Filament\Resources\TransactionResource
```

## 9. Catatan Pengembangan Lanjutan

- Dashboard Filament dapat ditambah widget saldo per wallet.
- Grafik pemasukan dan pengeluaran bulanan bisa ditambahkan setelah flow transaksi stabil.
- Bila user bertambah dan permission makin detail, bisa ditambah Filament Shield atau policy Laravel.
