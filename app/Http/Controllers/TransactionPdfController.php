<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TransactionPdfService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransactionPdfController extends Controller
{
    public function __invoke(Transaction $transaction, TransactionPdfService $transactionPdfService): BinaryFileResponse
    {
        abort_unless(auth()->check(), 403);
        abort_unless(auth()->user()->can('View:Transaction'), 403);

        if (! auth()->user()->isSuperAdmin() && ((int) $transaction->created_by !== (int) auth()->id())) {
            abort(403);
        }

        try {
            $pdf = $transactionPdfService->generate($transaction);
        } catch (\Throwable $throwable) {
            report($throwable);

            abort(500, 'PDF transaksi gagal dibuat. Periksa konfigurasi generator PDF.');
        }

        return response()->file($pdf['path'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $pdf['filename']),
        ]);
    }
}
