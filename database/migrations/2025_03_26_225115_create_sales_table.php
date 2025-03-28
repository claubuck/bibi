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
        Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->date('sale_date');
                $table->string('product_name');
                $table->string('product_category');
                $table->decimal('unit_price', 10, 2);
                $table->integer('quantity');
                $table->decimal('total_sale', 12, 2);
                $table->string('customer_type'); // Ej: Minorista, Mayorista
                $table->string('payment_method');
                $table->string('region');
                $table->string('sales_person');
                $table->timestamps();

                $table->index('sale_date');
                $table->index('product_category');
                $table->index('region');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
