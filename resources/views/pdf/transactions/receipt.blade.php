<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Transaksi {{ $receipt['transaction_no'] }}</title>
    <style>
        @page {
            margin: 40px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #1e293b;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
        }

        /* Header Styling */
        .header {
            border-bottom: 2px solid #112E81;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .brand-container {
            display: inline-block;
            width: 50%;
            vertical-align: middle;
        }

        .brand-name {
            font-size: 22px;
            font-weight: bold;
            color: #112E81;
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        .title-container {
            display: inline-block;
            width: 48%;
            text-align: right;
            vertical-align: middle;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-subtitle {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Summary Info Card */
        .summary-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .summary-card td {
            padding: 16px 20px;
            vertical-align: middle;
        }

        .summary-left {
            border-right: 1px solid #e2e8f0;
            width: 50%;
        }

        .label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .amount-value {
            font-size: 26px;
            font-weight: 800;
            color: {{ $receipt['amount_color'] }};
            margin: 4px 0;
        }

        .status-badge {
            display: inline-block;
            background-color: {{ $receipt['status_bg'] }};
            color: {{ $receipt['status_color'] }};
            font-weight: bold;
            font-size: 9px;
            padding: 3px 8px;
            border-radius: 99px;
            text-transform: uppercase;
        }

        .value-tx-no {
            font-family: Courier, monospace;
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }

        .value-date {
            font-size: 11px;
            font-weight: bold;
            color: #334155;
            margin-top: 2px;
        }

        /* Section Title */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #112E81;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 24px 0 10px 0;
            border-left: 3px solid #112E81;
            padding-left: 8px;
        }

        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .details-table td {
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .details-key {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            width: 32%;
        }

        .details-value {
            color: #0f172a;
            font-weight: 500;
        }

        /* Image Attachment */
        .proof-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background-color: #f8fafc;
            text-align: center;
        }

        .proof-box img {
            max-height: 240px;
            max-width: 100%;
            border-radius: 4px;
        }

        /* Footer Notice */
        .notice-box {
            background-color: #eff6ff;
            border-left: 4px solid #112E81;
            border-radius: 4px;
            color: #334155;
            padding: 10px 14px;
            margin-top: 32px;
            font-size: 9.5px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <!-- Header block -->
    <div class="header">
        <div class="brand-container">
            <img src="{{ public_path('images/logo1.png') }}" style="height: 30px;" alt="Logo">
        </div>
        <div class="title-container">
            <div class="doc-title">Bukti Transaksi</div>
            <div class="doc-subtitle">Dibuat otomatis oleh sistem</div>
        </div>
    </div>

    <!-- Summary Box -->
    <table class="summary-card" cellspacing="0" cellpadding="0">
        <tr>
            <td class="summary-left">
                <div class="label">Nominal Transaksi</div>
                <div class="amount-value">{{ $receipt['amount'] }}</div>
                <div class="status-badge">{{ $receipt['status_text'] }}</div>
            </td>
            <td>
                <div class="label">Nomor Transaksi</div>
                <div class="value-tx-no">{{ $receipt['transaction_no'] }}</div>

                <div class="label" style="margin-top: 12px; margin-bottom: 2px;">Tanggal Input</div>
                <div class="value-date">{{ $receipt['created_at'] }}</div>
            </td>
        </tr>
    </table>

    <!-- Transaction Details -->
    <div class="section-title">Detail Transaksi</div>
    <table class="details-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="details-key">Tipe Transaksi</td>
            <td class="details-value"><strong>{{ $receipt['transaction_type'] }}</strong></td>
        </tr>
        <tr>
            <td class="details-key">Tanggal Transaksi</td>
            <td class="details-value">{{ $receipt['transaction_date'] }}</td>
        </tr>
        <tr>
            <td class="details-key">Rekening / Wallet</td>
            <td class="details-value">{{ $receipt['wallet_name'] }}</td>
        </tr>
        <tr>
            <td class="details-key">Kategori</td>
            <td class="details-value">{{ $receipt['category_name'] }}</td>
        </tr>
        <tr>
            <td class="details-key">Target Tabungan</td>
            <td class="details-value">{{ $receipt['goal_name'] }}</td>
        </tr>
        <tr>
            <td class="details-key">Dibuat Oleh</td>
            <td class="details-value">{{ $receipt['created_by_name'] }}</td>
        </tr>
        <tr>
            <td class="details-key">Email Pembuat</td>
            <td class="details-value" style="font-family: Courier, monospace;">{{ $receipt['created_by_email'] }}</td>
        </tr>
        <tr>
            <td class="details-key">Deskripsi / Catatan</td>
            <td class="details-value" style="line-height: 1.4;">{{ $receipt['description'] ?: '-' }}</td>
        </tr>
    </table>

    <!-- Attachment -->
    @if ($receipt['image_path'] && file_exists($receipt['image_path']))
        <div class="section-title">Lampiran Bukti Gambar</div>
        <div class="proof-box">
            <img src="{{ $receipt['image_path'] }}" alt="Bukti Gambar">
        </div>
    @endif

    <!-- Security/Footer Notice -->
    <div class="notice-box">
        <strong>PENTING:</strong> Dokumen PDF ini diterbitkan secara sah oleh aplikasi Money Tracker sebagai bukti
        otentik pencatatan keuangan. Harap simpan dokumen digital ini sebagai arsip transaksi Anda.
    </div>

</body>

</html>
