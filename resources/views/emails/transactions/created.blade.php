<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi {{ $payload['transaction_no'] }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased;">
    <div style="padding: 40px 16px; background-color: #f1f5f9;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
            
            <!-- Brand Header -->
            <div style="background-color: #112E81; padding: 32px; text-align: center; color: #ffffff;">
                <div style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <img src="{{ $message->embed(public_path('images/logo2.png')) }}" style="height: 38px;" alt="Money Tracker Logo">
                </div>
                <h1 style="font-size: 24px; font-weight: 700; margin: 0; letter-spacing: -0.02em; line-height: 1.2;">Notifikasi Transaksi</h1>
                <p style="font-size: 14px; opacity: 0.85; margin: 8px 0 0 0; line-height: 1.4;">Email bukti transaksi otomatis dari sistem pencatatan keuangan Anda.</p>
            </div>

            <!-- Main Content Area -->
            <div style="padding: 32px;">
                
                <!-- Main Summary Box -->
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; text-align: center;">
                    <span style="font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">Nominal Transaksi</span>
                    <span style="color: {{ $payload['amount_color'] }}; font-size: 34px; font-weight: 800; letter-spacing: -0.03em; display: block; line-height: 1.1; margin-bottom: 12px;">
                        {{ $payload['amount_sign'] }} {{ $payload['amount'] }}
                    </span>
                    <div style="background-color: {{ $payload['status_bg'] }}; color: {{ $payload['status_color'] }}; font-weight: 700; font-size: 11px; padding: 6px 14px; border-radius: 9999px; display: inline-block; letter-spacing: 0.03em;">
                        {{ $payload['status_text'] }}
                    </div>
                </div>

                <!-- Section Details Title -->
                <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 28px 0 12px; letter-spacing: -0.01em;">Rincian Informasi</h3>

                <!-- Key-Value Table -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">No. Transaksi</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-family: monospace;">{{ $payload['transaction_no'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Tipe Transaksi</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $payload['transaction_type'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Tanggal Transaksi</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $payload['transaction_date'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Rekening</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $payload['wallet_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Kategori</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $payload['category_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Target Tabungan</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $payload['goal_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500; border-bottom: 1px solid #e2e8f0;">Dibuat Oleh</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $payload['created_by_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 40%; background-color: #f8fafc; padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 500;">Deskripsi</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #334155; line-height: 1.5;">{{ $payload['description'] }}</td>
                    </tr>
                </table>

                <!-- Action Button -->
                <div style="margin-top: 32px; text-align: center;">
                    <a href="{{ $payload['pdf_url'] }}" style="display: inline-block; background-color: #112E81; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 14px 28px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(17, 46, 129, 0.2), 0 2px 4px -1px rgba(17, 46, 129, 0.1);">
                        Unduh Bukti PDF
                    </a>
                </div>

                <div style="margin-top: 24px; font-size: 13px; line-height: 1.6; color: #64748b; text-align: center;">
                    PDF bukti transaksi juga telah dilampirkan pada email ini untuk arsip pribadi Anda.
                </div>
            </div>

            <!-- Footer area -->
            <div style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 32px; font-size: 12px; line-height: 1.6; color: #94a3b8; text-align: center;">
                Email ini dikirim otomatis oleh sistem Money Tracker. Harap tidak membalas email ini secara langsung.
            </div>
        </div>
    </div>
</body>
</html>
