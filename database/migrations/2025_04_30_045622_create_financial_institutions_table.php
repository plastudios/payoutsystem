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
        Schema::create('financial_institutions', function (Blueprint $table) {
            $table->id();
            $table->string('fiType')->nullable();
            $table->string('fiName')->nullable();
            $table->string('fiCode')->nullable();
            $table->string('fiShortCod')->nullable();
            $table->string('fiStatus')->nullable();
            $table->string('cardRoutingNo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_institutions');
    }
};
