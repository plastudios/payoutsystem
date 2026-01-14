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
        Schema::create('mfs_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id');
            $table->string('reference_key')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('wallet_number');
            $table->enum('method', ['bKash', 'Nagad']);
            $table->string('merchant_id');
            $table->enum('status', ['Pending', 'Success', 'Failed'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfs_payouts');
    }
};
