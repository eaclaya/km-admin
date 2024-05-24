<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $date = new \Datetime();
        $created_at = $date->format('Y-m-d H:i:s');

        Schema::dropIfExists('finance_daybook_entry');
        Schema::dropIfExists('finance_daybook_entry_item');
        Schema::dropIfExists('finance_catalogue_classification');
        Schema::dropIfExists('finance_catalogue_item');

        Schema::create('finance_daybook_entry', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sort_account');
            $table->integer('sort_company');

            $table->integer('account_id')->nullable();
            $table->integer('organization_company_id')->nullable();

            $table->string('description')->nullable();

            $table->integer('user_id')->nullable();
            $table->integer('real_user_id')->nullable();

            $table->decimal('partial', 12,2)->nullable();
            $table->decimal('debit', 12,2)->nullable();
            $table->decimal('havings', 12,2)->nullable();

            $table->timestamps();
            $table->softDeletes()->nullable();
        });

        Schema::create('finance_daybook_entry_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('finance_daybook_entry_id');

            $table->integer('account_id')->nullable();
            $table->integer('organization_company_id')->nullable();

            $table->string('description')->nullable();

            $table->integer('finance_catalogue_item_id')->nullable();
            $table->string('model')->nullable();
            $table->integer('model_id')->nullable();

            $table->decimal('partial', 12,2)->nullable();
            $table->decimal('debit', 12,2)->nullable();
            $table->decimal('havings', 12,2)->nullable();

            $table->timestamps();
            $table->softDeletes()->nullable();
        });

        Schema::create('finance_catalogue_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('sort')->nullable();
            $table->integer('items_qty')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
        });

        DB::table('finance_catalogue_classification')->insert([
            [
                'name' => 'Clase',
                'sort' => 1,
                'items_qty' => 1,
                'color' => '#9BC2E6',
                'created_at' => $created_at
            ],
            [
                'name' => 'Grupo',
                'sort' => 2,
                'items_qty' => 1,
                'color' => '#BDD7EE',
                'created_at' => $created_at
            ],
            [
                'name' => 'Cuenta Mayor',
                'sort' => 3,
                'items_qty' => 1,
                'color' => '#FFE699',
                'created_at' => $created_at
            ],
            [
                'name' => 'Auxiliar',
                'sort' => 4,
                'items_qty' => 3,
                'color' => '#D9D9D9',
                'created_at' => $created_at
            ],
            [
                'name' => 'Sub cuentas del Aux',
                'sort' => 5,
                'items_qty' => 2,
                'color' => '#F8F8F8',
                'created_at' => $created_at
            ],
            [
                'name' => 'Sub cuentas de sub cuenta Aux',
                'sort' => 6,
                'items_qty' => 2,
                'color' => '#F8F8F8',
                'created_at' => $created_at
            ]
        ]);

        Schema::create('finance_catalogue_item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('finance_account_name')->nullable();
            $table->integer('sub_item_id')->nullable();
            $table->integer('sort')->nullable();
            $table->string('finance_catalogue_classification_sort')->nullable();
            $table->string('model')->nullable();
            $table->integer('model_id')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
        });

        $items = $this->getItems();

        $sub = [
            0=>null,
            1=>null,
            2=>null,
            3=>null,
            4=>null,
            5=>null,
            6=>null
        ];

        $sortNameColumn = [
            0 => null,
            1 => 'Clase',
            2 => 'Grupo',
            3 => 'Cuenta_Mayor',
            4 => 'Auxiliar',
            5 => 'Sub_cuentas_del_Aux',
            6 => 'Sub_cuentas_de_sub_cuenta_Aux'
        ];

        for ($i=0; $i < count($items) ; $i++) {
            $items[$i]['classification'] = intval($items[$i]['classification']);
            if(intval($items[$i]['classification']) == 1){
                $sub[0] = null;
                $sub[1] = null;
                $sub[2] = null;
                $sub[3] = null;
                $sub[4] = null;
                $sub[5] = null;
                $sub[6] = null;
            }
            if(isset($items[$i+1]) && (intval($items[$i+1]['classification']) > intval($items[$i]['classification']))){
                $id = DB::table('finance_catalogue_item')->insertGetId([
                    'finance_account_name' => $items[$i]['Nombre_de_la_Cuenta'],
                    'sub_item_id' => (intval($items[$i]['classification']) > 1 && isset($sub[intval($items[$i]['classification']) - 1])) ? $sub[intval($items[$i]['classification']) - 1] : null,
                    'sort' => $items[$i][$sortNameColumn[intval($items[$i]['classification'])]],
                    'finance_catalogue_classification_sort' => $items[$i]['classification'],
                    'model' => isset($items[$i]['model']) ? $items[$i]['model'] : null,
                    'model_id' => isset($items[$i]['model_id']) ? $items[$i]['model_id'] : null,
                    'created_at' => $created_at,
                ]);
                $id = is_array($id) ? $id[0] : $id;
                $sub[intval($items[$i]['classification'])] = $id;
            }else{
                DB::table('finance_catalogue_item')->insert([
                    'finance_account_name' => $items[$i]['Nombre_de_la_Cuenta'],
                    'sub_item_id' => (intval($items[$i]['classification']) > 1 && isset($sub[intval($items[$i]['classification']) - 1])) ? $sub[intval($items[$i]['classification']) - 1] : null,
                    'sort' => $items[$i][$sortNameColumn[intval($items[$i]['classification'])]],
                    'finance_catalogue_classification_sort' => $items[$i]['classification'],
                    'model' => isset($items[$i]['model']) ? $items[$i]['model'] : null,
                    'model_id' => isset($items[$i]['model_id']) ? $items[$i]['model_id'] : null,
                    'created_at' => $created_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_daybook_entry');
        Schema::dropIfExists('finance_daybook_entry_item');
        Schema::dropIfExists('finance_catalogue_classification');
        Schema::dropIfExists('finance_catalogue_item');
    }

    public function getItems(): array
    {
        return
            [
                [
                    "Clase" => "1",
                    "Grupo" => "0",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "1",
                    "Nombre_de_la_Cuenta" => "ACTIVO"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Activo Corriente"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Caja"
                ],
                ...$this->getSubByAccount(1,1,1,0,3,'Caja Tienda'),
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Caja Chica"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Caja General"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Bancos"
                ],
                ...$this->getBancos(),
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "cuentas por cobrar"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Prestamos Capital Clientes C/P"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Intereses Clientes C/P"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Pago tardio"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "4",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Inventario"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "4",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Almacen "
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Pagos por anticiado"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Primas de Seguro "
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Rentas Pagadas por anticipado"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto Sobre Ventas Pagado"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Intereses Pagados Por adelantado"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "5",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Papeleria y Utiles"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "6",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Material de Aseo aun no consumido"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "7",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Anticipo a proveedores"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "8",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Publicidad aun no consumida"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Activo No Corriente"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Propiedad Planta y Equipo"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Mobiliario y Equipo"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Herramientas"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Equipo de Reparto"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Edificios "
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "5",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Terrenos"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "6",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Vehiculos"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Activo Intangible"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Aplicaciones informaticas"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Marcas"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "4",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Otros Activos"
                ],
                [
                    "Clase" => "1",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "4",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Depositos en garantias"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "0",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "1",
                    "Nombre_de_la_Cuenta" => "PASIVO"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Pasivo Corriente"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Cuentas por pagar C/P"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Soporte Otros"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Proveedores (acreedores comerciales)"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Documentos por pagar C/P"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Prestamos Bancarios C/P"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "5",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Sueldos a empleados"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "6",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto Sobre Ventas a pagar"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "7",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto Sobre la Renta a Pagar"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "8",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto municipal por pagar"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "9",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Pagos a Cuenta ISR"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Obligaciones por provisiones"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Prov. Por beneficio a empleado"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Prov 14 avo"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Prov. 13 avo"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Pasivo No corriente"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Cuentas por pagar L/P"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Acreedores Varios"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Documentos por pagar L/P"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Prestamos Bancarios L/P"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Prestamo Bancario"
                ],
                [
                    "Clase" => "2",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Hipotecas por pagar"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "0",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "1",
                    "Nombre_de_la_Cuenta" => "PATRIMONIO"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Aportacion"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Capital"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Aportacion del dueno"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Resultados Acumulados "
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Utilidad o perdida Ejercicio Anterior"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Utilidad o perdida Ejercicio Anterior"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Utilidad o perdida del periodo"
                ],
                [
                    "Clase" => "3",
                    "Grupo" => "2",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Utilidad o perdida del periodo"
                ],
                [
                    "Clase" => "4",
                    "Grupo" => "0",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "1",
                    "Nombre_de_la_Cuenta" => "Cuentas de Orden"
                ],
                [
                    "Clase" => "5",
                    "Grupo" => "0",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "1",
                    "Nombre_de_la_Cuenta" => "CUENTAS DE RESULTADO ACREEDORAS"
                ],
                [
                    "Clase" => "5",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "INGRESOS"
                ],
                [
                    "Clase" => "5",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "TOTAL INGRESOS"
                ],
                ...$this->getSubByAccount(5,1,1,0,3,'Ingreso por venta'),
                [
                    "Clase" => "6",
                    "Grupo" => "0",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "1",
                    "Nombre_de_la_Cuenta" => "CUENTAS DE RESULTADO DE EGRESOS"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "0",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "2",
                    "Nombre_de_la_Cuenta" => "Gastos"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Gastos de Operación"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Sueldos a empleados"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Provisionamiento 13avo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Provisionamiento 14avo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Comisiones a vendedores"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "5",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Seguro social"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "6",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Alquiler del Local"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "7",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Energia electrica"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "8",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Agua "
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "9",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Plan de Telefono"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "10",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Publicidad "
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "11",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Combustibles y Lubricantes"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "12",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Reparacion de Vehiculo "
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "13",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Mantenimiento preventivo Vehiculo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "14",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gastos de Viaje"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "15",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Consumo de papeleria"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "16",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Seguro de mercaderia"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "17",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gasto por entrega de mercaderia a Cliente"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "18",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto sobre volumen de Ventas"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "19",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Depreciacion Equipo de Reparto"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "20",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Depreciacion Mobiliario y equipo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "21",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Equipo menor"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "1",
                    "Auxiliar" => "22",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gastos Varios "
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Gastos de Venta"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Sueldos a empleados"
                ],
                ...$this->getSubByAccount(6,1,2,1,4,'Sueldos a empleados'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Provisionamiento 13avo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Provisionamiento 14avo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Comisiones a vendedores"
                ],
                ...$this->getSubByAccount(6,1,2,4,4,'Comisiones a vendedores'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "5",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Seguro social"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "6",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Alquiler del Local"
                ],
                ...$this->getSubByAccount(6,1,2,6,4,'Alquiler del Local'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "7",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Energia electrica"
                ],
                ...$this->getSubByAccount(6,1,2,7,4,'Energia electrica'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "8",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Agua"
                ],
                ...$this->getSubByAccount(6,1,2,8,4,'Agua'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "9",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Plan de Telefono"
                ],
                ...$this->getSubByAccount(6,1,2,9,4,'Plan de Telefono'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "10",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Publicidad"
                ],
                ...$this->getSubByAccount(6,1,2,10,4,'Publicidad'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "11",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Combustibles y Lubricantes"
                ],
                ...$this->getSubByAccount(6,1,2,11,4,'Combustibles y Lubricantes'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "12",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Reparacion de Vehiculo"
                ],
                ...$this->getSubByAccount(6,1,2,12,4,'Reparacion de Vehiculo'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "13",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Envios"
                ],
                ...$this->getSubByAccount(6,1,2,13,4,'Envios'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "14",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gastos Menores"
                ],
                ...$this->getSubByAccount(6,1,2,13,4,'Gastos Menores'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "15",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Mantenimiento preventivo Vehiculo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "16",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gastos de Viaje"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "17",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Consumo de papeleria"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "18",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Seguro de mercaderia"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "19",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gasto por entrega de mercaderia a Cliente"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "20",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto sobre volumen de Ventas"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "21",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Depreciacion Equipo de Reparto"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "22",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Depreciacion Mobiliario y equipo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "23",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Equipo menor"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "2",
                    "Auxiliar" => "24",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gastos Varios"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Gastos de Administracion"
                ],
                ...$this->getSubByCompany(6,1,3,0,3,'Gastos de Administracion'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "3",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Gastos de Operaciones"
                ],
                ...$this->getSubByCompany(6,1,3,0,3,'Gastos de Operaciones'),
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "4",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Gastos Personales"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "4",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Gastos Personales"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Gastos Financieros"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Intereses pagados ya devengados"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Comisiones Pagadas a los Bancos"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Descto por pronto pago Concedido a Clientes"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "5",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Tasa de Seguridad"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "6",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Productos Financieros"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "6",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Intereses Cobrados ya devengados"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "6",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Descto por pronto pago de prov. Y Acreedores"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "7",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Otros Gastos"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "7",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Donaciones"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "7",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Perdida en ventas de Activo Fijo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "7",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Faltante de Caja chica"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "7",
                    "Auxiliar" => "4",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Faltante"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "8",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Otros Productos"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "8",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Utilidad en ventas de Activo Fijo"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "8",
                    "Auxiliar" => "2",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Sobrante de Caja chica"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "8",
                    "Auxiliar" => "3",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Sobrante"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "9",
                    "Auxiliar" => "0",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "3",
                    "Nombre_de_la_Cuenta" => "Impuesto Sobre la Renta a Pagar"
                ],
                [
                    "Clase" => "6",
                    "Grupo" => "1",
                    "Cuenta_Mayor" => "9",
                    "Auxiliar" => "1",
                    "Sub_cuentas_del_Aux" => "null",
                    "classification" => "4",
                    "Nombre_de_la_Cuenta" => "Impuesto Sobre la Renta a Pagar"
                ]
            ];
    }

    public function getSubByAccount($class, $group, $cM, $aux, $classifications, $accountName = ''): array
    {
        if ($classifications === 3){
            $auxiliar = 'Auxiliar';
            $subCuentasDelAux = 'Sub_cuentas_del_Aux';
        }else{
            $auxiliar = 'Sub_cuentas_del_Aux';
            $subCuentasDelAux = 'Sub_cuentas_de_sub_cuenta_Aux';
        }
        $aux = $aux + 1;
        $auxTwo = $aux + 1;
        $auxThree = $aux + 2;
        $accountName = trim($accountName) !== '' ? $accountName . " " : '';
        $classificationsUp = $classifications + 1;
        $classificationsDown = $classifications + 2;
        return [
//            up
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "null",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."KM motos",
                "model" => "organization_company",
                "model_id" => "1",
            ],
//            down
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "1",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS COMAYAGUA",
                "model" => "accounts",
                "model_id" => "1",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "2",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS COMAYAGÜELA",
                "model" => "accounts",
                "model_id" => "4",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "3",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS JUTICALPA",
                "model" => "accounts",
                "model_id" => "8",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "4",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS CATACAMAS - OLANCHO",
                "model" => "accounts",
                "model_id" => "5",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "5",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS CATACAMAS 2",
                "model" => "accounts",
                "model_id" => "39",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "6",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LA PAZ",
                "model" => "accounts",
                "model_id" => "33",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "7",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS GUALACO",
                "model" => "accounts",
                "model_id" => "21",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "8",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS CULMI",
                "model" => "accounts",
                "model_id" => "22",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "9",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS SPS #1",
                "model" => "accounts",
                "model_id" => "23",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "10",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS SPS 2",
                "model" => "accounts",
                "model_id" => "37",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "11",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS JUTIAPA",
                "model" => "accounts",
                "model_id" => "7",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "12",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - TOROCAGUA",
                "model" => "accounts",
                "model_id" => "36",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "13",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS TELA",
                "model" => "accounts",
                "model_id" => "42",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "14",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS EL PROGRESO",
                "model" => "accounts",
                "model_id" => "41",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "15",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS TOCOA",
                "model" => "accounts",
                "model_id" => "34",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "16",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS PEÑA BLANCA",
                "model" => "accounts",
                "model_id" => "58",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "17",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS SABA",
                "model" => "accounts",
                "model_id" => "44",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "18",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - COUNTRY TEGUS",
                "model" => "accounts",
                "model_id" => "72",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "19",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - EL PROGRESO 2",
                "model" => "accounts",
                "model_id" => "79",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "20",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SPS 3",
                "model" => "accounts",
                "model_id" => "83",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "21",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS JUTICALPA 2",
                "model" => "accounts",
                "model_id" => "97",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $aux,
                $subCuentasDelAux => "22",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "SAC"
            ],
//            up
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "null",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."MOTOREPUESTOS TODO EN UNO",
                "model" => "organization_company",
                "model_id" => "2",
            ],
//            down
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "1",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "COMAYAGUA 2 - ALL IN ONE",
                "model" => "accounts",
                "model_id" => "9",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "2",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM COMAYAGUA 4 - ALL IN ONE",
                "model" => "accounts",
                "model_id" => "26",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "3",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS COMAYAGUA #5",
                "model" => "accounts",
                "model_id" => "59",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "4",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS COMAYAGUA #6",
                "model" => "accounts",
                "model_id" => "60",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "5",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "Comayagua 7"
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "6",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "ALL IN ONE - CHOLUTECA",
                "model" => "accounts",
                "model_id" => "3",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "7",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS SIGUATEPEQUE #1",
                "model" => "accounts",
                "model_id" => "43",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "8",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS SAN LORENZO",
                "model" => "accounts",
                "model_id" => "53",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "9",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS LA ESPERANZA #1",
                "model" => "accounts",
                "model_id" => "54",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "10",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SANTA ROSA",
                "model" => "accounts",
                "model_id" => "64",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "11",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SANTA BARBARA",
                "model" => "accounts",
                "model_id" => "66",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "12",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - TALANGA",
                "model" => "accounts",
                "model_id" => "67",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "13",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - AZACUALPA SANTA BARBARA",
                "model" => "accounts",
                "model_id" => "74",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "14",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LAS VEGAS SANTA BARBARA",
                "model" => "accounts",
                "model_id" => "77",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "15",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SIGUATEPEQUE #2",
                "model" => "accounts",
                "model_id" => "71",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "16",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LA ESPERANZA #2",
                "model" => "accounts",
                "model_id" => "69",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "17",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - CHOLUTECA 2",
                "model" => "accounts",
                "model_id" => "80",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "18",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - CHOLUTECA 3",
                "model" => "accounts",
                "model_id" => "81",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "19",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SANTA ROSA COPAN #2",
                "model" => "accounts",
                "model_id" => "82",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "20",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - TAULABE",
                "model" => "accounts",
                "model_id" => "90",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "21",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - JESUS DE OTORO",
                "model" => "accounts",
                "model_id" => "84",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "22",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SAN MARCOS",
                "model" => "accounts",
                "model_id" => "99",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "23",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SIGUATEPEQUE #3",
                "model" => "accounts",
                "model_id" => "100",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxTwo,
                $subCuentasDelAux => "24",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTO - QUIMISTAN",
                "model" => "accounts",
                "model_id" => "101",
            ],
//            up
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "null",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."KM CARS",
                "model" => "organization_company",
                "model_id" => "3",
            ],
//            down
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "1",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - COMAYAGUA #3",
                "model" => "accounts",
                "model_id" => "24",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "2",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LA CUESTA #1",
                "model" => "accounts",
                "model_id" => "27",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "3",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS LA LIBERTAD",
                "model" => "accounts",
                "model_id" => "28",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "4",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - ENTRADA COPAN",
                "model" => "accounts",
                "model_id" => "65",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "5",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS GRACIAS LEMPIRA",
                "model" => "accounts",
                "model_id" => "47",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "6",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - MARCALA #1",
                "model" => "accounts",
                "model_id" => "45",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "7",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS DANLI",
                "model" => "accounts",
                "model_id" => "55",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "8",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS EL PARAISO",
                "model" => "accounts",
                "model_id" => "56",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "9",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS PUERTO CORTES",
                "model" => "accounts",
                "model_id" => "49",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "10",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS CHOLOMA",
                "model" => "accounts",
                "model_id" => "57",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "11",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - VILLANUEVA",
                "model" => "accounts",
                "model_id" => "35",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "12",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS LA CEIBA",
                "model" => "accounts",
                "model_id" => "48",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "13",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - MARCALA 2",
                "model" => "accounts",
                "model_id" => "63",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "14",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LA ENTRADA #2",
                "model" => "accounts",
                "model_id" => "70",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "15",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - YORO, YORO",
                "model" => "accounts",
                "model_id" => "78",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "16",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - MORAZAN YORO",
                "model" => "accounts",
                "model_id" => "75",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "17",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - SANTA CRUZ DE YOJOA",
                "model" => "accounts",
                "model_id" => "73",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "18",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LA CUESTA #2",
                "model" => "accounts",
                "model_id" => "76",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "19",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - TROJES",
                "model" => "accounts",
                "model_id" => "91",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "20",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS SAN JUAN",
                "model" => "accounts",
                "model_id" => "46",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "21",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - DANLI 2",
                "model" => "accounts",
                "model_id" => "98",
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                $auxiliar => $auxThree,
                $subCuentasDelAux => "22",
                "classification" => $classificationsDown,
                "Nombre_de_la_Cuenta" => "KM MOTOS - LEPAERA",
                "model" => "accounts",
                "model_id" => "102",
            ],
        ];
    }

    public function getSubByCompany($class, $group, $cM, $aux, $classifications, $accountName = ''): array
    {
        $accountName = trim($accountName) !== '' ? $accountName . " " : '';
        $classificationsUp = $classifications + 1;
        $classificationsDown = $classifications + 2;
        $aux = $aux + 1;
        $auxTwo = $aux + 1;
        $auxThree = $aux + 2;
        return [
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "null",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."KM motos",
                "model" => "organization_company",
                "model_id" => "1",
            ],
            ...$this->getSubSubByGasto($class, $group, $cM, $aux, $classificationsDown,'organization_company', 1, $accountName),
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $auxTwo,
                "Sub_cuentas_del_Aux" => "null",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."MOTOREPUESTOS TODO EN UNO",
                "model" => "organization_company",
                "model_id" => "2",
            ],
            ...$this->getSubSubByGasto($class, $group, $cM, $auxTwo, $classificationsDown,'organization_company', 1, $accountName),
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $auxThree,
                "Sub_cuentas_del_Aux" => "null",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."KM CARS",
                "model" => "organization_company",
                "model_id" => "3",
            ],
            ...$this->getSubSubByGasto($class, $group, $cM, $auxThree, $classificationsDown,'organization_company', 1, $accountName),
        ];
    }

    public function getSubSubByGasto($class, $group, $cM, $aux, $classifications, $model, $modelId, $accountName = ''): array
    {
        $accountName = trim($accountName) !== '' ? $accountName . " " : '';
        $classificationsUp = $classifications;
        return [
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "1",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Sueldos de Empleados",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "2",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Provisionamiento 13avo",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "3",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Provisionamiento 14avo",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "4",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Seguro Social",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "5",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Decimo Tercer",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "6",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Decimo Cuarto",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "7",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Alquiler del Local Oficinas",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "8",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Energia electrica",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "9",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Agua",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "10",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Plan de Telefono",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "11",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Combustibles y Lubricantes",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "12",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Reparacion de Mobiliario y equipo",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "13",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Consumo de Papeleria",
                "model" => $model,
                "model_id" => $modelId
            ],
            [
                "Clase" => $class,
                "Grupo" => $group,
                "Cuenta_Mayor" => $cM,
                "Auxiliar" => $aux,
                "Sub_cuentas_del_Aux" => "14",
                "classification" => $classificationsUp,
                "Nombre_de_la_Cuenta" => $accountName."Mantenimiento de Moviliario y Equipo ",
                "model" => $model,
                "model_id" => $modelId
            ],
        ];
    }

    public function getBancos(): array
    {
        return [
//            Banco Atantida
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "1",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banco Atantida"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "1",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 120520068202"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "1",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 120520110053"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "1",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 120520148533"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "1",
                "Sub_cuentas_del_Aux" => "4",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Dolares) - 120120249269"
            ],
//            Banco Azteca
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "2",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banco Azteca"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "2",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 75080104256826"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "2",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 77860107221984"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "2",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 77860113320711"
            ],
//            Banco Occidente
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "3",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banco Occidente"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "3",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 217010454710"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "3",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 214300038685"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "3",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 217010529710"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "3",
                "Sub_cuentas_del_Aux" => "4",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Dolares) - 214300038685"
            ],
//            BAC Credomatic
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "4",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "BAC Credomatic"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "4",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 740518501"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "4",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 741833011"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "4",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 745979781"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "4",
                "Sub_cuentas_del_Aux" => "4",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "KM MOTO S. DE R.L DE C.V. (Lempiras) - 1487408"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "4",
                "Sub_cuentas_del_Aux" => "5",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 742478961"
            ],
//            Tigo Money
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "5",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Tigo Money"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "5",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 3326-4857"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "5",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 9977-1867"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "5",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 9438-2083"
            ],
//            Banrural
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "6",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banrural"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "6",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 34110087889"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "6",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 34110090023"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "6",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 8110520476"
            ],
//            Ficohsa
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Ficohsa"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 8371261"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 200015653147"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 200015380267"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "4",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Dolares) - 200013223636"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "5",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Dolares) - 200015653198"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "7",
                "Sub_cuentas_del_Aux" => "6",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Dolares) - 200015386494"
            ],
//            Banco LAFISE
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banco LAFISE"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 135504004105"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 135504004106"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Lempiras) - 135504004109"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "4",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Dolares) - 135604000308"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "5",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Dolares) - 135604000310"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "8",
                "Sub_cuentas_del_Aux" => "6",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osbin Josue Garrido Banegas (Dolares) - 135604000311"
            ],
//            DAVIVIENDA
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "9",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "DAVIVIENDA"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "9",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 1171181319"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "9",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 1171182803"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "9",
                "Sub_cuentas_del_Aux" => "3",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Dolares) - 1171182455"
            ],
//            Banco CUSCATLAN
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "10",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banco CUSCATLAN"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "10",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Osman Roberto Garrido Banegas (Lempiras) - 217050003746"
            ],
//            Banco Promerica
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "11",
                "Sub_cuentas_del_Aux" => "null",
                "classification" => "4",
                "Nombre_de_la_Cuenta" => "Banco Promerica"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "11",
                "Sub_cuentas_del_Aux" => "1",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Lempiras) - 10000001422968"
            ],
            [
                "Clase" => "1",
                "Grupo" => "1",
                "Cuenta_Mayor" => "2",
                "Auxiliar" => "11",
                "Sub_cuentas_del_Aux" => "2",
                "classification" => "5",
                "Nombre_de_la_Cuenta" => "Rossy Marilin Cardenas Lopez (Dolares) - 20000001435581"
            ],
        ];
    }
};
