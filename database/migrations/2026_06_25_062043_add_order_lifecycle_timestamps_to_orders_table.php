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
        Schema::table('orders', function ($table) {

            $table->timestamp('ready_for_pickup_at')->nullable();

            $table->timestamp('on_the_way_at')->nullable();

            $table->timestamp('cancelled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function ($table) {

            $table->dropColumn([
                'ready_for_pickup_at',
                'on_the_way_at',
                'cancelled_at',
            ]);
        });
    }
};
