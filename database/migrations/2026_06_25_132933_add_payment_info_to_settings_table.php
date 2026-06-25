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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('easypaisa_title')
                ->nullable()
                ->after('tax_percentage');

            $table->string('easypaisa_number')
                ->nullable()
                ->after('easypaisa_title');
            $table->string('jazzcash_title')
                ->nullable()
                ->after('easypaisa_number');

            $table->string('jazzcash_number')
                ->nullable()
                ->after('jazzcash_title');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('settings', function (Blueprint $table) {

        $table->dropColumn([
            'easypaisa_title',
            'easypaisa_number',
            'jazzcash_title',
            'jazzcash_number',
        ]);
    });
}
};
