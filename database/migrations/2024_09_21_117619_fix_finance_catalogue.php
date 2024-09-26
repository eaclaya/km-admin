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

        $subItem = DB::table('finance_catalogue_item')->where('finance_account_name', 'Cuentas por pagar C/P')->first();
        $subItemId = $subItem->id;
        $clasifications = $subItem->finance_catalogue_classification_sort + 1;

        DB::table('finance_catalogue_item')->insert([
            'finance_account_name' => 'Anticipo de Cliente',
            'sub_item_id' => $subItemId,
            'finance_catalogue_classification_sort' => $clasifications,
            'sort' => 10
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
