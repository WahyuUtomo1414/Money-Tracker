<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class TransactionPdfService
{
    /**
     * @return array{path: string, filename: string}
     */
    public function generate(Transaction $transaction): array
    {
        $transaction->loadMissing(['wallet', 'category', 'goal', 'createdBy']);

        $directory = storage_path('app/private/transactions/pdf');
        File::ensureDirectoryExists($directory);

        $filename = Str::slug((string) $transaction->transaction_no) . '.pdf';
        $payloadPath = $directory . DIRECTORY_SEPARATOR . Str::slug((string) $transaction->transaction_no) . '.json';
        $outputPath = $directory . DIRECTORY_SEPARATOR . $filename;

        File::put($payloadPath, json_encode($this->buildPayload($transaction), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $process = new Process([
            $this->resolvePythonBinary(),
            base_path('bin/generate_transaction_pdf.py'),
            $payloadPath,
            $outputPath,
        ]);

        $process->setTimeout((int) config('services.transaction_pdf.timeout', 30));
        $process->run();

        File::delete($payloadPath);

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput() ?: 'Generator PDF transaksi gagal dijalankan.'));
        }

        if (! File::exists($outputPath)) {
            throw new RuntimeException('File PDF transaksi tidak berhasil dibuat.');
        }

        return [
            'path' => $outputPath,
            'filename' => $filename,
        ];
    }

    protected function resolvePythonBinary(): string
    {
        $candidates = array_filter([
            config('services.transaction_pdf.python_binary'),
            env('HOME') ? env('HOME') . '/.cache/codex-runtimes/codex-primary-runtime/dependencies/python/bin/python3' : null,
            '/usr/bin/python3',
            '/opt/homebrew/bin/python3',
            'python3',
            'python',
        ]);

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $this->canRunReportLab($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('Binary Python untuk generator PDF transaksi tidak ditemukan.');
    }

    protected function canRunReportLab(string $pythonBinary): bool
    {
        $process = new Process([
            $pythonBinary,
            '-c',
            'import reportlab',
        ]);

        $process->setTimeout(10);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * @return array<string, string|null>
     */
    protected function buildPayload(Transaction $transaction): array
    {
        $transactionType = TransactionTypeEnum::from((string) $transaction->transaction_type);

        return [
            'app_name' => 'Money Tracker',
            'transaction_no' => (string) $transaction->transaction_no,
            'transaction_date' => $transaction->transaction_date?->format('d M Y') ?? '-',
            'transaction_type' => $transactionType->label(),
            'amount' => 'Rp ' . number_format((float) $transaction->amount, 0, ',', '.'),
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
