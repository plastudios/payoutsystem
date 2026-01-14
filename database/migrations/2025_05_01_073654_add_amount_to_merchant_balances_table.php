<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('merchant_balances', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->after('type');
        });
    }
    
    public function down()
    {
        Schema::table('merchant_balances', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
    
};
