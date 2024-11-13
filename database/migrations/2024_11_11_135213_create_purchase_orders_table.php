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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['draft', 'sent', 'received', 'cancelled']);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
