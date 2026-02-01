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
        Schema::table('mfs_payouts', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->string('mfs_transaction_id')->nullable()->after('completed_at');
            $table->foreignId('agent_id')->nullable()->after('mfs_transaction_id')->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable()->after('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mfs_payouts', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn(['completed_at', 'mfs_transaction_id', 'agent_id', 'remarks']);
        });
    }
};
