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
        Schema::table('orders', function (Blueprint $table) {
             $table->decimal('sgst', 10, 2)->default(0)->after('delivery_fee');
            $table->decimal('cgst', 10, 2)->default(0)->after('sgst');
            $table->decimal('igst', 10, 2)->default(0)->after('cgst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
                       $table->dropColumn(['sgst', 'cgst', 'igst']);
        });
    }
};
