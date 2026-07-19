# Database Money Tracker

## 1. Ringkasan

Dokumen ini merangkum struktur database awal berdasarkan ERD yang diberikan, dengan penyesuaian teknis agar cocok untuk implementasi Laravel migration.

Catatan penting:

- Dokumen ini mengikuti ERD awal dari user.
- Ada beberapa rekomendasi perbaikan naming dan tipe data untuk kebutuhan finansial.
- Untuk nilai uang, disarankan memakai `decimal(18,2)` dibanding `double`.

## 2. Daftar Tabel

- `users`
- `category`
- `wallet`
- `users_wallet`
- `goals`
- `transaction`
- `transaction_ledger`

## 3. Detail Tabel

### 3.1 users

Fungsi:
Menyimpan data akun pengguna aplikasi.

Kolom:

| Kolom      | Tipe            | Null | Keterangan       |
| ---------- | --------------- | ---- | ---------------- |
| id         | bigint unsigned | no   | primary key      |
| name       | varchar(255)    | no   | nama user        |
| email      | varchar(128)    | no   | email user, unik |
| password   | varchar(255)    | no   | password hash    |
| avatar     | varchar(255)    | yes  | path file avatar |
| created_at | timestamp       | yes  | bawaan Laravel   |
| updated_at | timestamp       | yes  | bawaan Laravel   |

Catatan:
Di ERD terlihat ada typo `avater`, namun implementasi disarankan memakai `avatar`.

### 3.2 category

Fungsi:
Menyimpan kategori untuk wallet atau transaksi.

Kolom:

| Kolom       | Tipe            | Null | Keterangan               |
| ----------- | --------------- | ---- | ------------------------ |
| id          | bigint unsigned | no   | primary key              |
| name        | varchar(128)    | no   | nama kategori            |
| type        | varchar(128)    | no   | penanda konteks kategori |
| description | text            | yes  | deskripsi kategori       |
| created_at  | timestamp       | yes  | bawaan Laravel           |
| updated_at  | timestamp       | yes  | bawaan Laravel           |

Rekomendasi:
Kolom `type` bisa dikontrol menggunakan enum aplikasi, misalnya `wallet` dan `transaction`.

### 3.3 wallet

Fungsi:
Menyimpan sumber dana atau rekening.

Kolom:

| Kolom        | Tipe            | Null | Keterangan               |
| ------------ | --------------- | ---- | ------------------------ |
| id           | bigint unsigned | no   | primary key              |
| category_id  | bigint unsigned | no   | foreign key ke category  |
| bank_name    | varchar(128)    | no   | nama bank atau platform  |
| account_no   | varchar(128)    | no   | nomor rekening atau akun |
| account_name | varchar(128)    | no   | nama pemilik rekening    |
| description  | text            | yes  | catatan tambahan         |
| created_at   | timestamp       | yes  | bawaan Laravel           |
| updated_at   | timestamp       | yes  | bawaan Laravel           |

### 3.4 users_wallet

Fungsi:
Tabel pivot relasi user dengan wallet.

Kolom:

| Kolom      | Tipe            | Null | Keterangan            |
| ---------- | --------------- | ---- | --------------------- |
| id         | bigint unsigned | no   | primary key           |
| user_id    | bigint unsigned | no   | foreign key ke users  |
| wallet_id  | bigint unsigned | no   | foreign key ke wallet |
| created_at | timestamp       | yes  | opsional              |
| updated_at | timestamp       | yes  | opsional              |

Rekomendasi:
Tambahkan unique index pada pasangan `user_id` dan `wallet_id`.

### 3.5 goals

Fungsi:
Menyimpan target tabungan per wallet.

Kolom:

| Kolom         | Tipe            | Null | Keterangan            |
| ------------- | --------------- | ---- | --------------------- |
| id            | bigint unsigned | no   | primary key           |
| wallet_id     | bigint unsigned | no   | foreign key ke wallet |
| name          | varchar(128)    | no   | nama target           |
| description   | text            | yes  | deskripsi target      |
| target_amount | decimal(18,2)   | no   | target nominal        |
| target_date   | date            | no   | target tanggal        |
| created_at    | timestamp       | yes  | bawaan Laravel        |
| updated_at    | timestamp       | yes  | bawaan Laravel        |

### 3.6 transaction

Fungsi:
Menyimpan data transaksi utama.

Kolom:

| Kolom            | Tipe            | Null | Keterangan                                 |
| ---------------- | --------------- | ---- | ------------------------------------------ |
| id               | bigint unsigned | no   | primary key                                |
| uuid             | char(36)        | no   | identitas unik publik                      |
| transaction_no   | varchar(50)     | no   | nomor transaksi unik                       |
| transaction_type | enum            | no   | `topup`, `payment`, `refund`, `adjustment` |
| transaction_date | date            | no   | tanggal transaksi                          |
| amount           | decimal(18,2)   | no   | nominal transaksi                          |
| description      | text            | yes  | deskripsi                                  |
| image            | varchar(255)    | yes  | path bukti transaksi                       |
| wallet_id        | bigint unsigned | no   | foreign key ke wallet                      |
| category_id      | bigint unsigned | yes  | foreign key ke category                    |
| goal_id          | bigint unsigned | yes  | foreign key ke goals                       |
| created_at       | timestamp       | yes  | bawaan Laravel                             |
| updated_at       | timestamp       | yes  | bawaan Laravel                             |

Catatan:

- ERD menulis `goals_id`, tetapi implementasi disarankan `goal_id`.
- `uuid` dan `transaction_no` wajib unik.

### 3.7 transaction_ledger

Fungsi:
Menyimpan histori perubahan saldo wallet.

Kolom:

| Kolom            | Tipe            | Null | Keterangan                                           |
| ---------------- | --------------- | ---- | ---------------------------------------------------- |
| id               | bigint unsigned | no   | primary key                                          |
| uuid             | char(36)        | no   | identitas unik ledger                                |
| transaction_no   | varchar(50)     | no   | nomor transaksi referensi                            |
| transaction_date | date            | no   | tanggal transaksi                                    |
| ref_id           | bigint unsigned | no   | id transaksi asal, mengarah ke `transaction.id`      |
| ref_type         | varchar(128)    | no   | tipe transaksi asal, misalnya `topup` atau `payment` |
| amount           | decimal(18,2)   | no   | nominal perubahan                                    |
| last_amount      | decimal(18,2)   | no   | saldo sebelum transaksi                              |
| end_amount       | decimal(18,2)   | no   | saldo setelah transaksi                              |
| wallet_id        | bigint unsigned | no   | foreign key ke wallet                                |
| category_id      | bigint unsigned | yes  | foreign key ke category                              |
| created_at       | timestamp       | yes  | bawaan Laravel                                       |
| updated_at       | timestamp       | yes  | bawaan Laravel                                       |

## 4. Relasi Antar Tabel

- `wallet.category_id` -> `category.id`
- `users_wallet.user_id` -> `users.id`
- `users_wallet.wallet_id` -> `wallet.id`
- `goals.wallet_id` -> `wallet.id`
- `transaction.wallet_id` -> `wallet.id`
- `transaction.category_id` -> `category.id`
- `transaction.goal_id` -> `goals.id`
- `transaction_ledger.wallet_id` -> `wallet.id`
- `transaction_ledger.category_id` -> `category.id`
- `transaction_ledger.ref_id` -> `transaction.id` secara logis di level aplikasi

## 5. Enum Aplikasi

### 5.1 Transaction Type Enum

Nilai enum:

| Value      | Prefix | Makna                               |
| ---------- | ------ | ----------------------------------- |
| topup      | TPU    | penambahan saldo                    |
| payment    | PAY    | pengurangan saldo karena pembayaran |
| refund     | RFD    | pengembalian dana                   |
| adjustment | ADJ    | koreksi manual saldo                |

Rekomendasi implementasi:

- Enum PHP menjadi source of truth.
- Migration dapat memakai kolom `enum` sesuai 4 nilai di atas.
- Prefix nomor transaksi diambil dari enum yang sama.

## 6. Rekomendasi Migration Order

Urutan migration yang direkomendasikan:

1. `create_users_table`
2. `create_categories_table`
3. `create_wallets_table`
4. `create_users_wallet_table`
5. `create_goals_table`
6. `create_transactions_table`
7. `create_transaction_ledgers_table`

## 7. Rekomendasi Index dan Constraint

### 7.1 Unique

- `users.email`
- `transaction.uuid`
- `transaction.transaction_no`
- `transaction_ledger.uuid`
- `users_wallet (user_id, wallet_id)`

### 7.2 Index

- `transaction.transaction_date`
- `transaction.transaction_type`
- `transaction.wallet_id`
- `transaction.category_id`
- `transaction.goal_id`
- `transaction_ledger.ref_id`
- `transaction_ledger.transaction_no`
- `transaction_ledger.wallet_id`
- `transaction_ledger.transaction_date`

## 8. Contoh Draft Migration Laravel

```php
Schema::create('transaction', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('transaction_no', 50)->unique();
    $table->enum('transaction_type', ['topup', 'payment', 'refund', 'adjustment']);
    $table->date('transaction_date');
    $table->decimal('amount', 18, 2);
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->foreignId('wallet_id')->constrained('wallet');
    $table->foreignId('category_id')->nullable()->constrained('category');
    $table->foreignId('goal_id')->nullable()->constrained('goals');
    $table->timestamps();
});
```

```php
Schema::create('transaction_ledger', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('transaction_no', 50);
    $table->date('transaction_date');
    $table->unsignedBigInteger('ref_id');
    $table->string('ref_type', 128);
    $table->decimal('amount', 18, 2);
    $table->decimal('last_amount', 18, 2);
    $table->decimal('end_amount', 18, 2);
    $table->foreignId('wallet_id')->constrained('wallet');
    $table->foreignId('category_id')->nullable()->constrained('category');
    $table->timestamps();

    $table->index('ref_id');
    $table->index('transaction_no');
    $table->index(['wallet_id', 'transaction_date']);
});
```

## 9. Catatan Teknis Penting

- Untuk domain keuangan, hindari `double` karena rawan pembulatan.
- Jika nantinya butuh histori perubahan data, pertimbangkan kolom audit seperti `created_by` dan `updated_by`.
- Ledger sebaiknya diperlakukan sebagai turunan dari tabel `transaction`, bukan input manual terpisah.
- Saat transaksi diubah atau dihapus, sistem perlu menghitung ulang `last_amount` dan `end_amount` untuk ledger setelah titik perubahan pada wallet terkait.
- Jika transaksi nanti bisa transfer antar wallet, struktur ledger masih bisa dipakai, tetapi flow posting perlu diperluas.
- Relasi `users_wallet` dipakai untuk pembatasan akses data dan penerima email transaksi.
- Query data untuk user biasa harus memakai aturan `created_by = auth()->id()` atau `wallet_id` termasuk wallet yang di-assign ke user.
- Test yang memakai `RefreshDatabase` harus dijalankan pada environment `testing` dengan SQLite memory, bukan database MySQL lokal.
- Hindari menjalankan `migrate:fresh`, `migrate:fresh --seed`, atau `db:wipe` pada database lokal yang berisi data kerja tanpa backup.
- Buat backup sebelum reset database: `mysqldump -h 127.0.0.1 -P 3306 -u root money-tracker > ~/Downloads/money-tracker-backup.sql`.
