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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id');
            $table->string('amount');
            $table->string('currency');
            $table->string('remarks');
            $table->string('bankCode');
            $table->string('bankShortCode');
            $table->string('benType');
            $table->string('txnChannel');
            $table->string('beneficiaryAcc');
            $table->string('beneficiaryName');
            $table->string('beneficiaryEmail');
            $table->string('routingNumber');
            $table->string('txnChannelCode');
            $table->string('merchant_id')->nullable();
            $table->text('api_response')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
