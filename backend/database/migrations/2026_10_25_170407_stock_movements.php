<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StockMovementType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_batch_id')
                  ->constrained('stock_batches')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users');

            $table->string('type')->default(StockMovementType::entry->value);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->string('reason')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
