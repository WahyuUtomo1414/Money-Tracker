<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use Illuminate\Support\Str;

class TransactionLedgerService
{
    public function create(Transaction $transaction): TransactionLedger
    {
        return $this->postLedger($transaction, $this->resolveSignedAmount($transaction));
    }

    public function update(Transaction $oldTransaction, Transaction $newTransaction): void
    {
        $this->delete($oldTransaction);
        $this->create($newTransaction);
    }

    public function delete(Transaction $transaction): TransactionLedger
    {
        return $this->postLedger($transaction, $this->resolveSignedAmount($transaction) * -1);
    }

    protected function postLedger(Transaction $transaction, float $signedAmount): TransactionLedger
    {
        $lastAmount = (float) (TransactionLedger::query()
            ->where('wallet_id', $transaction->wallet_id)
            ->latest('transaction_date')
            ->latest('id')
            ->value('end_amount') ?? 0);

        $endAmount = $lastAmount + $signedAmount;

        return TransactionLedger::query()->create([
            'uuid' => (string) Str::uuid(),
            'transaction_no' => $transaction->transaction_no,
            'transaction_date' => now()->toDateString(),
            'ref_id' => $transaction->id,
            'ref_type' => Transaction::class,
            'amount' => $signedAmount,
            'last_amount' => $lastAmount,
            'end_amount' => $endAmount,
            'wallet_id' => $transaction->wallet_id,
            'category_id' => $transaction->category_id,
            'active' => true,
        ]);
    }

    protected function resolveSignedAmount(Transaction $transaction): float
    {
        $transactionType = TransactionTypeEnum::from((string) $transaction->transaction_type);
        $amount = (float) $transaction->amount;

        return match ($transactionType) {
            TransactionTypeEnum::Payment => $amount * -1,
            default => $amount,
        };
    }
}
