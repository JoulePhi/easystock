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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained('item_categories');
            $table->string('unit');
            $table->decimal('minimum_stock', 10, 2);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
