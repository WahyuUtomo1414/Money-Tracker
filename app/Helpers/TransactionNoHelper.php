<?php

namespace App\Helpers;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;

class TransactionNoHelper
{
    public static function generate(TransactionTypeEnum $transactionType): string
    {
        $now = now();
        $prefix = $transactionType->prefix();
        $basePrefix = "{$prefix}/{$now->format('Y/m')}/";

        $latestTransactionNo = Transaction::query()
            ->withoutGlobalScopes()
            ->where('transaction_no', 'like', "{$basePrefix}%")
            ->orderByDesc('transaction_no')
            ->value('transaction_no');

        $lastSequence = 0;

        if ($latestTransactionNo) {
            $parts = explode('/', $latestTransactionNo);
            $lastSequence = (int) ($parts[3] ?? 0);
        }

        $nextSequence = str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);

        return "{$basePrefix}{$nextSequence}";
    }
}
