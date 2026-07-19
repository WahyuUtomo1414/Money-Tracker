<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public array $pdf,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Transaksi ' . $this->transaction->transaction_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transactions.created',
            with: [
                'payload' => $this->buildEmailPayload(),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdf['path'])
                ->as($this->pdf['filename'])
                ->withMime('application/pdf'),
        ];
    }

    protected function buildEmailPayload(): array
    {
        $transaction = $this->transaction;
        $transaction->loadMissing(['wallet', 'category', 'goal', 'createdBy']);

        $type = \App\Enums\TransactionTypeEnum::tryFrom($transaction->transaction_type);
        $isExpense = $type === \App\Enums\TransactionTypeEnum::Payment;

        return [
            'pdf_url' => route('transactions.pdf.show', $transaction),
            'amount_color' => $isExpense ? '#e11d48' : '#059669',
            'amount_sign' => $isExpense ? '-' : '+',
            'amount' => 'Rp ' . number_format((float) $transaction->amount, 0, ',', '.'),
            'status_text' => match($type) {
                \App\Enums\TransactionTypeEnum::Topup => 'TOP UP BERHASIL',
                \App\Enums\TransactionTypeEnum::Payment => 'PEMBAYARAN BERHASIL',
                \App\Enums\TransactionTypeEnum::Refund => 'PENGEMBALIAN DANA BERHASIL',
                \App\Enums\TransactionTypeEnum::Adjustment => 'PENYESUAIAN BERHASIL',
                default => 'TRANSAKSI BERHASIL',
            },
            'status_bg' => $isExpense ? '#ffe4e6' : '#dcfce7',
            'status_color' => $isExpense ? '#9f1239' : '#166534',
            'transaction_no' => $transaction->transaction_no,
            'transaction_type' => $type?->label() ?? '-',
            'transaction_date' => $transaction->transaction_date?->format('d M Y') ?? '-',
            'wallet_name' => $transaction->wallet?->display_name ?? '-',
            'category_name' => $transaction->category?->name ?? '-',
            'goal_name' => $transaction->goal?->name ?? '-',
            'created_by_name' => $transaction->createdBy?->name ?? '-',
            'description' => $transaction->description ?: '-',
        ];
    }
}
