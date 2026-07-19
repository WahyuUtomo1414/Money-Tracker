<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori dasar yang dipakai oleh form Rekening dan Transaksi.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rekening Bank',
                'type' => 'wallet',
                'description' => 'Kategori untuk rekening tabungan atau giro bank.',
            ],
            [
                'name' => 'Dompet Digital',
                'type' => 'wallet',
                'description' => 'Kategori untuk e-wallet seperti GoPay, OVO, DANA, atau ShopeePay.',
            ],
            [
                'name' => 'Tunai',
                'type' => 'wallet',
                'description' => 'Kategori untuk uang kas atau dompet fisik.',
            ],
            [
                'name' => 'Kartu Kredit',
                'type' => 'wallet',
                'description' => 'Kategori untuk rekening kartu kredit.',
            ],
            [
                'name' => 'Tabungan',
                'type' => 'wallet',
                'description' => 'Kategori untuk rekening tabungan.',
            ],
            [
                'name' => 'Gaji',
                'type' => 'transaction',
                'description' => 'Pemasukan rutin dari gaji atau payroll.',
            ],
            [
                'name' => 'Bonus',
                'type' => 'transaction',
                'description' => 'Pemasukan tambahan seperti bonus, insentif, atau komisi.',
            ],
            [
                'name' => 'Makanan dan Minuman',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk makan, minum, dan kebutuhan konsumsi harian.',
            ],
            [
                'name' => 'Transportasi',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk bensin, parkir, tol, ojek online, atau transport umum.',
            ],
            [
                'name' => 'Belanja Bulanan',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk kebutuhan rumah tangga dan belanja rutin.',
            ],
            [
                'name' => 'Tagihan',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk listrik, air, internet, telepon, dan cicilan.',
            ],
            [
                'name' => 'Kesehatan',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk obat, dokter, asuransi, atau kebutuhan kesehatan.',
            ],
            [
                'name' => 'Hiburan',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk rekreasi, langganan digital, dan aktivitas hiburan.',
            ],
            [
                'name' => 'Pendidikan',
                'type' => 'transaction',
                'description' => 'Pengeluaran untuk kursus, sekolah, buku, atau pelatihan.',
            ],
            [
                'name' => 'Tabungan dan Investasi',
                'type' => 'transaction',
                'description' => 'Alokasi dana untuk tabungan, investasi, atau target keuangan.',
            ],
            [
                'name' => 'Lainnya',
                'type' => 'transaction',
                'description' => 'Kategori transaksi umum yang belum masuk kategori lain.',
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                ],
                [
                    'description' => $category['description'],
                    'active' => true,
                    'created_by' => 1,
                ],
            );
        }
    }
}
