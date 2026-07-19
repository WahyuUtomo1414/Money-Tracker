@php
    $transaction->loadMissing(['wallet', 'category', 'goal', 'createdBy']);
    $pdfUrl = route('transactions.pdf.show', $transaction);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi {{ $transaction->transaction_no }}</title>
</head>
<body style="margin:0; padding:0; background-color:#eef2ff; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="padding:32px 16px;">
        <div style="max-width:680px; margin:0 auto; background-color:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 18px 45px rgba(15, 23, 42, 0.12);">
            <div style="background:linear-gradient(135deg, #163a8c 0%, #1d4ed8 100%); padding:28px 32px; color:#ffffff;">
                <div style="font-size:14px; letter-spacing:1.6px; text-transform:uppercase; opacity:.82;">Notifikasi Transaksi</div>
                <div style="font-size:28px; font-weight:700; margin-top:8px;">Bukti transaksi berhasil dibuat</div>
                <div style="font-size:14px; line-height:22px; margin-top:10px; opacity:.9;">
                    Email ini dikirim otomatis setelah transaksi tersimpan di Money Tracker.
                </div>
            </div>

            <div style="padding:28px 32px 18px;">
                <div style="background:#f8fafc; border:1px solid #dbeafe; border-radius:18px; padding:22px 24px;">
                    <div style="font-size:13px; color:#475569;">Nomor Transaksi</div>
                    <div style="font-size:22px; font-weight:700; margin-top:6px;">{{ $transaction->transaction_no }}</div>
                    <div style="font-size:34px; font-weight:700; color:#15803d; margin-top:14px;">
                        Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                    </div>
                    <div style="margin-top:10px; display:inline-block; background:#dcfce7; color:#166534; border-radius:999px; padding:8px 14px; font-size:12px; font-weight:700;">
                        TRANSAKSI BERHASIL
                    </div>
                </div>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:24px; border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 0 8px; font-size:18px; font-weight:700; color:#163a8c;">Rincian Transaksi</td>
                    </tr>
                </table>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b;">Tipe Transaksi</td>
                        <td style="padding:14px 16px; font-size:14px; font-weight:700;">{{ \App\Enums\TransactionTypeEnum::from($transaction->transaction_type)->label() }}</td>
                    </tr>
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b; border-top:1px solid #e2e8f0;">Tanggal Transaksi</td>
                        <td style="padding:14px 16px; font-size:14px; font-weight:700; border-top:1px solid #e2e8f0;">{{ $transaction->transaction_date?->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b; border-top:1px solid #e2e8f0;">Rekening</td>
                        <td style="padding:14px 16px; font-size:14px; font-weight:700; border-top:1px solid #e2e8f0;">{{ $transaction->wallet?->display_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b; border-top:1px solid #e2e8f0;">Kategori</td>
                        <td style="padding:14px 16px; font-size:14px; font-weight:700; border-top:1px solid #e2e8f0;">{{ $transaction->category?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b; border-top:1px solid #e2e8f0;">Target Tabungan</td>
                        <td style="padding:14px 16px; font-size:14px; font-weight:700; border-top:1px solid #e2e8f0;">{{ $transaction->goal?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b; border-top:1px solid #e2e8f0;">Dibuat Oleh</td>
                        <td style="padding:14px 16px; font-size:14px; font-weight:700; border-top:1px solid #e2e8f0;">{{ $transaction->createdBy?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="width:38%; background:#f8fafc; padding:14px 16px; font-size:13px; color:#64748b; border-top:1px solid #e2e8f0;">Deskripsi</td>
                        <td style="padding:14px 16px; font-size:14px; line-height:22px; border-top:1px solid #e2e8f0;">{{ $transaction->description ?: '-' }}</td>
                    </tr>
                </table>

                <div style="margin-top:28px;">
                    <a href="{{ $pdfUrl }}" style="display:inline-block; background:#163a8c; color:#ffffff; text-decoration:none; font-weight:700; padding:14px 20px; border-radius:12px;">
                        Lihat PDF Transaksi
                    </a>
                </div>

                <div style="margin-top:18px; font-size:13px; line-height:21px; color:#64748b;">
                    PDF bukti transaksi sudah dilampirkan pada email ini. Simpan lampiran tersebut sebagai arsip transaksi Anda.
                </div>
            </div>

            <div style="padding:18px 32px 28px; font-size:12px; line-height:20px; color:#94a3b8;">
                Email ini dikirim otomatis oleh sistem. Jika Anda tidak merasa membuat transaksi ini, segera periksa akun Money Tracker Anda.
            </div>
        </div>
    </div>
</body>
</html>
