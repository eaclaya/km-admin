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
        DB::table('finance_catalogue_item')->where('finance_account_name', 'Cuentas de Orden')->update(['finance_account_name' => 'COSTO']);
        DB::table('finance_catalogue_item')->where('finance_account_name', 'Prestamos Capital Clientes C/P')->update(['finance_account_name' => 'Clientes C/P']);
        DB::table('finance_catalogue_item')->insert([
            'finance_account_name' => 'Prestamos Capital C/P',
            'sub_item_id' => 129,
            'finance_catalogue_classification_sort' => 4,
            'sort' => 4
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
