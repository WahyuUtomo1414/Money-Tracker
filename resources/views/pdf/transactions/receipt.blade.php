<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Transaksi {{ $receipt['transaction_no'] }}</title>
    <style>
        @page {
            margin: 34px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            margin: 0;
        }

        .header {
            background: #163a8c;
            color: #ffffff;
            padding: 24px 28px;
        }

        .brand {
            font-size: 28px;
            font-weight: 700;
        }

        .header-title {
            float: right;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .5px;
            margin-top: 4px;
            text-align: right;
            text-transform: uppercase;
            width: 240px;
        }

        .header-subtitle {
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 0;
            margin-top: 4px;
            text-transform: none;
        }

        .card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            margin-top: 18px;
            width: 100%;
        }

        .card td {
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            vertical-align: top;
        }

        .label {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 6px;
        }

        .value {
            font-size: 14px;
            font-weight: 700;
        }

        .amount {
            color: #15803d;
            font-size: 30px;
            font-weight: 700;
        }

        .section-title {
            color: #163a8c;
            font-size: 17px;
            font-weight: 700;
            margin: 26px 0 10px;
        }

        .details {
            border-collapse: collapse;
            width: 100%;
        }

        .details td {
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            vertical-align: top;
        }

        .details .key {
            background: #f8fafc;
            color: #64748b;
            width: 34%;
        }

        .notice {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #334155;
            margin-top: 24px;
            padding: 14px 16px;
        }

        .proof {
            border: 1px solid #cbd5e1;
            margin-top: 10px;
            padding: 12px;
            text-align: center;
        }

        .proof img {
            max-height: 240px;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">
            Bukti Transaksi
            <div class="header-subtitle">Dokumen ini dibuat otomatis oleh sistem</div>
        </div>
        <div class="brand">{{ $receipt['app_name'] }}</div>
    </div>

    <table class="card" cellspacing="0" cellpadding="0">
        <tr>
            <td width="55%">
                <div class="label">Nomor Transaksi</div>
                <div class="value">{{ $receipt['transaction_no'] }}</div>
            </td>
            <td>
                <div class="label">Status</div>
                <div class="value">Berhasil</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Nominal Transaksi</div>
                <div class="amount">{{ $receipt['amount'] }}</div>
            </td>
            <td>
                <div class="label">Tanggal Input</div>
                <div class="value">{{ $receipt['created_at'] }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Transaksi</div>
    <table class="details" cellspacing="0" cellpadding="0">
        <tr>
            <td class="key">Tipe Transaksi</td>
            <td><strong>{{ $receipt['transaction_type'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Tanggal Transaksi</td>
            <td><strong>{{ $receipt['transaction_date'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Rekening</td>
            <td><strong>{{ $receipt['wallet_name'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Kategori</td>
            <td><strong>{{ $receipt['category_name'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Target Tabungan</td>
            <td><strong>{{ $receipt['goal_name'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Dibuat Oleh</td>
            <td><strong>{{ $receipt['created_by_name'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Email Penginput</td>
            <td><strong>{{ $receipt['created_by_email'] }}</strong></td>
        </tr>
        <tr>
            <td class="key">Deskripsi</td>
            <td>{{ $receipt['description'] }}</td>
        </tr>
    </table>

    @if ($receipt['image_path'] && file_exists($receipt['image_path']))
        <div class="section-title">Lampiran Bukti Gambar</div>
        <div class="proof">
            <img src="{{ $receipt['image_path'] }}" alt="Bukti Gambar">
        </div>
    @endif

    <div class="notice">
        Dokumen ini merupakan bukti transaksi yang dibuat oleh sistem Money Tracker. Simpan PDF ini sebagai arsip transaksi Anda.
    </div>
</body>
</html>
