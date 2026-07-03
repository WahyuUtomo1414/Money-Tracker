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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transaction_no', 50)->unique();
            $table->string('transaction_type', 32)->index();
            $table->date('transaction_date')->index();
            $table->decimal('amount', 18, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('wallet_id')->constrained('wallet');
            $table->foreignId('category_id')->nullable()->constrained('category');
            $table->foreignId('goal_id')->nullable()->constrained('goals');
            $this->base($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
