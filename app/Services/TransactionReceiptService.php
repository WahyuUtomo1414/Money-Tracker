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

    public function sendCreatedReceipt(Transaction $transaction): void
    {
        $transaction->loadMissing(['wallet', 'category', 'goal', 'createdBy']);

        $recipient = $transaction->createdBy?->email;

        if (! filled($recipient)) {
            return;
        }

        $pdf = $this->transactionPdfService->generate($transaction);

        Mail::to($recipient)->send(new TransactionCreatedMail($transaction, $pdf));
    }
}
