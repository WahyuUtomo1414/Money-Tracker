<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case Topup = 'topup';
    case Payment = 'payment';
    case Refund = 'refund';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Topup => 'Topup',
            self::Payment => 'Payment',
            self::Refund => 'Refund',
            self::Adjustment => 'Adjustment',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::Topup => 'TPU',
            self::Payment => 'PAY',
            self::Refund => 'RFD',
            self::Adjustment => 'ADJ',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }
}
