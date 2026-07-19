# BRD Money Tracker

## 1. Ringkasan Produk

Money Tracker adalah aplikasi web pribadi untuk mencatat arus uang antara pengguna dan pasangan, memonitor pengeluaran harian, mengelola saldo per wallet, dan memisahkan dana tabungan menuju target besar seperti tabungan nikah.

Sistem akan dibangun menggunakan Laravel sebagai backend dan Filament sebagai panel admin/internal utama.

## 2. Tujuan Bisnis

- Membantu pencatatan keuangan pribadi secara rapi dan konsisten.
- Menyediakan histori transaksi yang mudah ditelusuri.
- Mengetahui saldo terakhir setiap wallet tanpa hitung manual.
- Memisahkan transaksi umum dan transaksi yang terkait goal tabungan.
- Menjadi dasar pengambilan keputusan keuangan bersama.

## 3. Target Pengguna

- Pemilik aplikasi.
- Pasangan pemilik aplikasi.

Catatan:
Pada fase awal diasumsikan pengguna masih terbatas dan aplikasi dipakai secara private, bukan multi-tenant publik.

## 4. Ruang Lingkup Fase Awal

Fitur yang masuk fase awal:

- Login user.
- Manajemen master category.
- Manajemen wallet.
- Relasi user ke wallet.
- Manajemen goals atau target tabungan.
- Input transaksi.
- Pencatatan ledger saldo wallet.
- Upload bukti gambar transaksi bila diperlukan.
- Preview bukti gambar transaksi pada halaman detail.
- Generate PDF bukti transaksi.
- Pengiriman email bukti transaksi beserta lampiran PDF.
- Kirim ulang email bukti transaksi dari halaman detail transaksi.
- Monitoring histori transaksi dan saldo akhir.

Fitur yang belum menjadi fokus fase awal:

- Integrasi bank atau e-wallet API.
- Notifikasi WhatsApp atau push.
- Approval workflow.
- Multi-currency.
- Export laporan kompleks.

## 5. Proses Bisnis Inti

### 5.1 Wallet

Wallet merepresentasikan sumber atau tempat penyimpanan uang, misalnya rekening bank, dompet digital, atau pos dana tertentu.

Setiap wallet:

- Memiliki category.
- Dapat dihubungkan ke satu atau lebih user melalui tabel pivot `users_wallet`.
- Menjadi sumber saldo utama transaksi dan ledger.

### 5.2 Category

Category digunakan untuk mengelompokkan wallet atau transaksi sesuai kebutuhan pencatatan.

Contoh penggunaan:

- Kategori wallet: Bank, E-Wallet, Cash.
- Kategori transaksi: Belanja, Makan, Transport, Tabungan Nikah.

Kolom `type` pada category dipakai untuk membedakan konteks category.

### 5.3 Goals

Goals dipakai untuk target tabungan, misalnya:

- Tabungan nikah.
- Dana liburan.
- Dana darurat.

Satu goal terhubung ke satu wallet dan dapat direferensikan oleh transaksi tertentu.

### 5.4 Transaction

Transaksi adalah catatan aktivitas uang utama.

Tipe transaksi yang disepakati untuk fase awal:

- `topup`
- `payment`
- `refund`
- `adjustment`

Tipe transaksi wajib menggunakan `ENUM` pada level aplikasi, dan idealnya juga dijaga konsistensinya di database.

### 5.5 Transaction Number

Setiap transaksi wajib memiliki nomor unik `transaction_no`.

Aturan awal:

- Nomor dibuat otomatis oleh service khusus.
- Prefix nomor transaksi diambil dari enum tipe transaksi.
- Format nomor dapat menggunakan pola seperti: `PREFIX-YYYYMMDD-XXXX`.

Contoh prefix yang direkomendasikan:

- `topup` => `TPU`
- `payment` => `PAY`
- `refund` => `RFD`
- `adjustment` => `ADJ`

Contoh hasil:

- `TPU-20260703-0001`
- `PAY-20260703-0002`

Catatan:
Mapping prefix ini sebaiknya ditaruh dalam enum yang sama agar source of truth hanya satu.

### 5.6 Transaction Ledger

Ledger adalah catatan perubahan saldo wallet setelah transaksi terjadi.

Ledger menyimpan:

- nominal transaksi,
- saldo sebelumnya,
- saldo setelah transaksi,
- referensi ke data asal.

Dengan ledger, sistem dapat melakukan audit histori saldo dan mengurangi risiko salah hitung.

## 6. Kebutuhan Fungsional

### 6.1 Autentikasi

- User dapat login ke sistem.
- User yang belum terdaftar tidak dapat mengakses panel.

### 6.2 Master Category

- Admin dapat menambah category.
- Admin dapat mengubah category.
- Admin dapat melihat daftar category.
- Category memiliki `name`, `type`, dan `description`.

### 6.3 Master Wallet

- Admin dapat menambah wallet.
- Admin dapat mengubah wallet.
- Admin dapat melihat daftar wallet.
- Wallet memiliki `bank_name`, `account_no`, `account_name`, `description`, dan `category_id`.

### 6.4 Relasi User Wallet

- Sistem dapat menghubungkan user ke wallet.
- Satu user dapat memiliki lebih dari satu wallet.
- Satu wallet dapat dikaitkan ke lebih dari satu user bila diperlukan.
- Relasi `users_wallet` menjadi dasar pembatasan akses data dan penerima email transaksi.

### 6.5 Goals

- Admin dapat menambah goal.
- Admin dapat mengubah goal.
- Goal memiliki `wallet_id`, `name`, `description`, `target_amount`, dan `target_date`.

### 6.6 Transaction

- Admin dapat menambah transaksi.
- Sistem mengisi `uuid` otomatis.
- Sistem mengisi `transaction_no` otomatis.
- Sistem memvalidasi `transaction_type` berdasarkan enum.
- Sistem menyimpan `wallet_id` sebagai referensi utama.
- `category_id` bersifat opsional.
- `goal_id` bersifat opsional.
- Gambar bukti transaksi dapat disimpan pada kolom `image`.
- Sistem menampilkan bukti gambar pada halaman detail dan dapat membuka preview gambar.
- Sistem menyediakan action PDF pada transaksi.
- Sistem menyediakan action kirim email pada halaman detail transaksi.
- Saat transaksi dibuat atau email dikirim ulang, penerima email diambil dari user yang di-assign ke wallet transaksi.

### 6.7 Ledger

- Saat transaksi dibuat, sistem membuat baris ledger.
- Setiap baris ledger harus menyimpan `ref_id` yang mengarah ke `transaction.id`.
- Ledger menyimpan saldo sebelum dan sesudah transaksi.
- Ledger dapat ditelusuri berdasarkan wallet dan nomor transaksi.
- Saat transaksi diubah, sistem memperbarui ledger transaksi tersebut lalu menghitung ulang saldo transaksi setelahnya pada wallet terkait.
- Saat transaksi dihapus, sistem menghapus ledger berdasarkan `ref_id` lalu menghitung ulang saldo transaksi setelahnya pada wallet terkait.

## 7. Kebutuhan Non-Fungsional

- Aplikasi berbasis web dan mobile-friendly.
- Akses utama melalui panel Filament.
- Data transaksi harus konsisten dan dapat diaudit.
- Nomor transaksi harus unik.
- Upload file wajib dibatasi tipe dan ukuran file.
- Struktur kode harus mudah dikembangkan untuk laporan dan dashboard berikutnya.

## 8. Aturan Bisnis Awal

- Semua transaksi harus terkait ke satu wallet.
- `transaction_type` hanya boleh berisi nilai dari enum.
- `transaction_no` tidak boleh duplikat.
- Goal boleh kosong pada transaksi umum.
- Category boleh kosong pada transaksi yang belum perlu klasifikasi detail.
- Ledger hanya boleh dibuat oleh proses sistem, bukan input manual user.
- Hubungan transaksi ke ledger menggunakan `transaction.id -> transaction_ledger.ref_id`.
- Super admin dapat melihat semua data.
- User biasa hanya dapat melihat data yang dibuat sendiri atau data dari wallet yang di-assign ke user tersebut.
- Email bukti transaksi dikirim ke pemegang wallet, bukan hanya pembuat transaksi.
- Jika wallet belum memiliki user dengan email, sistem tidak mengirim email dan harus menampilkan notifikasi warning.

## 9. Asumsi Awal

- Aplikasi dipakai secara private.
- Belum ada kebutuhan approval antar user.
- Saldo wallet dihitung dari ledger sebagai sumber audit.
- ERD awal mengikuti gambar referensi yang diberikan user dan bisa disempurnakan pada iterasi berikutnya.

## 10. Risiko dan Catatan

- Tabel `category` saat ini dipakai untuk lebih dari satu konteks, sehingga perlu aturan `type` yang jelas.
- Tabel `transaction_ledger` membutuhkan logika saldo yang konsisten agar tidak terjadi mismatch.
- Perubahan transaksi lama dapat memengaruhi saldo semua transaksi setelahnya pada wallet yang sama, sehingga recalculation ledger wajib konsisten.
- PDF memakai DomPDF. Asset gambar resolusi besar, termasuk logo 5000x5000 atau bukti transaksi besar, dapat menyebabkan memory exhausted saat render PDF.
- Logo untuk email/PDF/Filament harus memakai varian warna yang sesuai background dan sebaiknya memakai versi kecil/optimized untuk PDF.
