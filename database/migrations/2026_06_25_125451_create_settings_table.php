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
        Schema::create('settings', function (Blueprint $table) {

            $table->id();

            $table->string('restaurant_name')->nullable();

            $table->string('phone')->nullable();

            $table->text('address')->nullable();

            $table->decimal('delivery_fee', 10, 2)
                ->default(200);

            $table->decimal('free_delivery_above', 10, 2)
                ->default(3000);

            $table->decimal('tax_percentage', 5, 2)
                ->default(0);

            $table->string('currency')
                ->default('PKR');

            $table->text('opening_hours')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
