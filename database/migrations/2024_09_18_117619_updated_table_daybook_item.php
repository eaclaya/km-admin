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
        Schema::table('finance_daybook_entry_item', function (Blueprint $table) {
            $table->integer('is_primary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_daybook_entry_item', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });
    }
};
