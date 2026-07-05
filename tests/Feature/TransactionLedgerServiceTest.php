<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionLedger;
use App\Models\Wallet;
use App\Services\TransactionLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionLedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_rebuilds_wallet_ledger_without_leaving_stale_rows(): void
    {
        [$wallet, $category] = $this->createWalletFixture();

        $firstTransaction = $this->createTransaction([
            'transaction_no' => 'TPU-001',
            'transaction_date' => '2026-07-01',
            'transaction_type' => 'topup',
            'amount' => 100000,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
        ]);

        $secondTransaction = $this->createTransaction([
            'transaction_no' => 'PAY-001',
            'transaction_date' => '2026-07-02',
            'transaction_type' => 'payment',
            'amount' => 25000,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
        ]);

        $service = app(TransactionLedgerService::class);
        $service->create($firstTransaction);
        $service->create($secondTransaction);

        $oldTransaction = $firstTransaction->replicate();
        $oldTransaction->id = $firstTransaction->id;

        $firstTransaction->update([
            'amount' => 150000,
            'transaction_date' => '2026-06-30',
        ]);

        $service->update($oldTransaction, $firstTransaction->fresh());

        $ledgers = TransactionLedger::query()
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $ledgers);
        $this->assertSame(['TPU-001', 'PAY-001'], $ledgers->pluck('transaction_no')->all());

        $this->assertSame('2026-06-30', $ledgers[0]->transaction_date->toDateString());
        $this->assertSame('150000.00', $ledgers[0]->amount);
        $this->assertSame('0.00', $ledgers[0]->last_amount);
        $this->assertSame('150000.00', $ledgers[0]->end_amount);

        $this->assertSame('2026-07-02', $ledgers[1]->transaction_date->toDateString());
        $this->assertSame('-25000.00', $ledgers[1]->amount);
        $this->assertSame('150000.00', $ledgers[1]->last_amount);
        $this->assertSame('125000.00', $ledgers[1]->end_amount);
    }

    public function test_delete_removes_deleted_transaction_from_wallet_ledger(): void
    {
        [$wallet, $category] = $this->createWalletFixture();

        $firstTransaction = $this->createTransaction([
            'transaction_no' => 'TPU-001',
            'transaction_date' => '2026-07-01',
            'transaction_type' => 'topup',
            'amount' => 100000,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
        ]);

        $secondTransaction = $this->createTransaction([
            'transaction_no' => 'PAY-001',
            'transaction_date' => '2026-07-02',
            'transaction_type' => 'payment',
            'amount' => 40000,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
        ]);

        $service = app(TransactionLedgerService::class);
        $service->create($firstTransaction);
        $service->create($secondTransaction);

        $oldTransaction = $secondTransaction->replicate();
        $oldTransaction->id = $secondTransaction->id;

        $secondTransaction->delete();

        $service->delete($oldTransaction);

        $ledgers = TransactionLedger::query()
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $this->assertCount(1, $ledgers);
        $this->assertSame('TPU-001', $ledgers[0]->transaction_no);
        $this->assertSame('100000.00', $ledgers[0]->end_amount);
    }

    protected function createWalletFixture(): array
    {
        $category = Category::query()->create([
            'name' => 'Cash',
            'type' => 'wallet',
            'description' => null,
            'active' => true,
        ]);

        $wallet = Wallet::query()->create([
            'category_id' => $category->id,
            'bank_name' => 'BCA',
            'account_no' => '1234567890',
            'account_name' => 'Main Wallet',
            'description' => null,
            'active' => true,
        ]);

        return [$wallet, $category];
    }

    protected function createTransaction(array $attributes): Transaction
    {
        return Transaction::query()->create([
            'uuid' => (string) Str::uuid(),
            'description' => null,
            'image' => null,
            'goal_id' => null,
            'active' => true,
            ...$attributes,
        ]);
    }
}
