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
}
