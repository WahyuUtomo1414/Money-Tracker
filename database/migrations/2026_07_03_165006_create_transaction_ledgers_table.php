<?php

use App\Traits\BaseModelSoftDeleteDefault;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use BaseModelSoftDeleteDefault;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_ledger', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transaction_no', 50)->index();
            $table->date('transaction_date');
            $table->unsignedBigInteger('ref_id');
            $table->string('ref_type', 128);
            $table->decimal('amount', 18, 2);
            $table->decimal('last_amount', 18, 2);
            $table->decimal('end_amount', 18, 2);
            $table->foreignId('wallet_id')->constrained('wallet');
            $table->foreignId('category_id')->nullable()->constrained('category');
            $this->base($table);

            $table->index(['wallet_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_ledger');
    }
};
