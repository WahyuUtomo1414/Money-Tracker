<?php

namespace App\Services;

use App\Mail\TransactionCreatedMail;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

class TransactionReceiptService
{
    public function __construct(
        protected TransactionPdfService $transactionPdfService,
    ) {}

    /**
     * @return array<int, string>
     */
    public function sendCreatedReceipt(Transaction $transaction): array
    {
        $transaction->loadMissing(['wallet.users', 'category', 'goal', 'createdBy']);

        $recipients = $transaction->wallet?->users?->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all() ?? [];

        if (empty($recipients)) {
            return [];
        }

        $pdf = $this->transactionPdfService->generate($transaction);

        Mail::to($recipients)->send(new TransactionCreatedMail($transaction, $pdf));

        return $recipients;
    }
}
