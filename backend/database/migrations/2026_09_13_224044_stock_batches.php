<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_batches', function(Blueprint $table){
            $table->id();

            //Relacionamento com a tabela de produtos
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            $table->string('batch_code')->nullable();
            $table->integer('quantity');
            $table->date('expiration_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
