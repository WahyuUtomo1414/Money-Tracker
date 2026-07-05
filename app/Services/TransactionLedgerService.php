<?php

namespace App\Services;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionLedgerService
{
    public function create(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $this->deleteLedger($transaction);
            $this->createLedger($transaction);
            $this->recalculateFrom(
                (int) $transaction->wallet_id,
                (string) $transaction->transaction_date,
                (int) $transaction->id,
            );
        });
    }

    public function update(Transaction $oldTransaction, Transaction $newTransaction): void
    {
        DB::transaction(function () use ($oldTransaction, $newTransaction): void {
            $this->deleteLedger($oldTransaction);
            $this->createLedger($newTransaction);

            if ((int) $oldTransaction->wallet_id !== (int) $newTransaction->wallet_id) {
                $this->recalculateFrom(
                    (int) $oldTransaction->wallet_id,
                    (string) $oldTransaction->transaction_date,
                    (int) $oldTransaction->id,
                );

                $this->recalculateFrom(
                    (int) $newTransaction->wallet_id,
                    (string) $newTransaction->transaction_date,
                    (int) $newTransaction->id,
                );

                return;
            }

            $fromDate = (string) $oldTransaction->transaction_date;
            $fromRefId = (int) $oldTransaction->id;

            if (
                (string) $newTransaction->transaction_date < (string) $oldTransaction->transaction_date
                || (
                    (string) $newTransaction->transaction_date === (string) $oldTransaction->transaction_date
                    && (int) $newTransaction->id < (int) $oldTransaction->id
                )
            ) {
                $fromDate = (string) $newTransaction->transaction_date;
                $fromRefId = (int) $newTransaction->id;
            }

            $this->recalculateFrom(
                (int) $newTransaction->wallet_id,
                $fromDate,
                $fromRefId,
            );
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $this->deleteLedger($transaction);
            $this->recalculateFrom(
                (int) $transaction->wallet_id,
                (string) $transaction->transaction_date,
                (int) $transaction->id,
            );
        });
    }

    protected function createLedger(Transaction $transaction): void
    {
        TransactionLedger::query()->create([
            'uuid' => (string) Str::uuid(),
            'transaction_no' => $transaction->transaction_no,
            'transaction_date' => $transaction->transaction_date,
            'ref_id' => $transaction->id,
            'ref_type' => $transaction->transaction_type,
            'amount' => $this->resolveSignedAmount($transaction),
            'last_amount' => 0,
            'end_amount' => 0,
            'wallet_id' => $transaction->wallet_id,
            'category_id' => $transaction->category_id,
            'active' => true,
        ]);
    }

    protected function deleteLedger(Transaction $transaction): void
    {
        TransactionLedger::withTrashed()
            ->where('ref_id', $transaction->id)
            ->forceDelete();
    }

    protected function recalculateFrom(int $walletId, string $fromDate, int $fromRefId): void
    {
        $lastAmount = (float) (TransactionLedger::query()
            ->where('wallet_id', $walletId)
            ->where(function ($query) use ($fromDate, $fromRefId): void {
                $query
                    ->where('transaction_date', '<', $fromDate)
                    ->orWhere(function ($query) use ($fromDate, $fromRefId): void {
                        $query
                            ->where('transaction_date', $fromDate)
                            ->where('ref_id', '<', $fromRefId);
                    });
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('ref_id')
            ->value('end_amount') ?? 0);

        $ledgers = TransactionLedger::query()
            ->where('wallet_id', $walletId)
            ->where(function ($query) use ($fromDate, $fromRefId): void {
                $query
                    ->where('transaction_date', '>', $fromDate)
                    ->orWhere(function ($query) use ($fromDate, $fromRefId): void {
                        $query
                            ->where('transaction_date', $fromDate)
                            ->where('ref_id', '>=', $fromRefId);
                    });
            })
            ->orderBy('transaction_date')
            ->orderBy('ref_id')
            ->get();

        foreach ($ledgers as $ledger) {
            $endAmount = $lastAmount + (float) $ledger->amount;

            $ledger->update([
                'last_amount' => $lastAmount,
                'end_amount' => $endAmount,
            ]);

            $lastAmount = $endAmount;
        }
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
