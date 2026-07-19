<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TransactionPdfService
{
    /**
     * @return array{path: string, filename: string}
     */
    public function generate(Transaction $transaction): array
    {
        ini_set('memory_limit', '512M');

        $transaction->loadMissing(['wallet', 'category', 'goal', 'createdBy']);

        $directory = storage_path('app/private/transactions/pdf');
        File::ensureDirectoryExists($directory);

        $filename = Str::slug((string) $transaction->transaction_no) . '.pdf';
        $outputPath = $directory . DIRECTORY_SEPARATOR . $filename;

        Pdf::loadView('pdf.transactions.receipt', [
            'transaction' => $transaction,
            'receipt' => $this->buildPayload($transaction),
        ])
            ->setPaper('a4')
            ->save($outputPath);

        return [
            'path' => $outputPath,
            'filename' => $filename,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    protected function buildPayload(Transaction $transaction): array
    {
        $transactionType = TransactionTypeEnum::from((string) $transaction->transaction_type);
        $isExpense = $transactionType === TransactionTypeEnum::Payment;
        $amountColor = $isExpense ? '#e11d48' : '#059669';
        $statusText = $isExpense ? 'Pengeluaran' : 'Pemasukan';
        $statusBg = $isExpense ? '#ffe4e6' : '#dcfce7';
        $statusColor = $isExpense ? '#9f1239' : '#166534';

        return [
            'app_name' => 'Money Tracker',
            'transaction_no' => (string) $transaction->transaction_no,
            'transaction_date' => $transaction->transaction_date?->format('d M Y') ?? '-',
            'transaction_type' => $transactionType->label(),
            'amount' => 'Rp ' . number_format((float) $transaction->amount, 0, ',', '.'),
            'amount_color' => $amountColor,
            'status_text' => $statusText,
            'status_bg' => $statusBg,
            'status_color' => $statusColor,
            'wallet_name' => $transaction->wallet?->display_name ?? '-',
            'category_name' => $transaction->category?->name ?? '-',
            'goal_name' => $transaction->goal?->name ?? '-',
            'description' => filled($transaction->description) ? (string) $transaction->description : '-',
            'created_by_name' => $transaction->createdBy?->name ?? '-',
            'created_by_email' => $transaction->createdBy?->email ?? '-',
            'created_at' => $transaction->created_at?->format('d M Y H:i') ?? '-',
            'image_path' => filled($transaction->image) ? storage_path('app/public/' . ltrim((string) $transaction->image, '/')) : null,
        ];
    }
}
