<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('payment_receipt')
                ->nullable()
                ->after('transaction_id');

            $table->timestamp('payment_verified_at')
                ->nullable()
                ->after('payment_receipt');

            $table->foreignId('payment_verified_by')
                ->nullable()
                ->after('payment_verified_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropConstrainedForeignId(
                'payment_verified_by'
            );

            $table->dropColumn([
                'payment_receipt',
                'payment_verified_at',
            ]);
        });
    }
};