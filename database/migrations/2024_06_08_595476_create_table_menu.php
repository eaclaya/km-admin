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
        Schema::dropIfExists('setup_menu');

        Schema::create('setup_menu', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('supra_menu_id')->nullable();

            $table->string('label')->nullable();
            $table->string('label_color')->nullable();

            $table->string('url')->nullable();
            $table->string('text')->nullable();

            $table->string('icon')->nullable();
            $table->string('can')->nullable();

            $table->timestamps();
            $table->softDeletes()->nullable();
        });

        $items = $this->getItems();
        foreach ($items as $item) {
            $id = null;
            $data = [];
            $data['supra_menu_id'] = null;
            $data['label'] = isset($item['label']) ? $item['label'] : null;
            $data['label_color'] = isset($item['label_color']) ? $item['label_color'] : null;
            $data['url'] = isset($item['url']) ? $item['url'] : '#';
            $data['text'] = isset($item['text']) ? $item['text'] : null;
            $data['icon'] = isset($item['icon']) ? $item['icon'] : null;
            $data['can'] = isset($item['can']) ? $item['can'] : null;

            $id = DB::table('setup_menu')->insertGetId($data);

            if (isset($item['submenu'])) {
                $supraData = [];
                foreach ($item['submenu'] as $subitem) {
                    $dataSub = [];
                    $dataSub['supra_menu_id'] = $id;
                    $dataSub['label'] = isset($subitem['label']) ? $subitem['label'] : null;
                    $dataSub['label_color'] = isset($subitem['label_color']) ? $subitem['label_color'] : null;
                    $dataSub['url'] = isset($subitem['url']) ? $subitem['url'] : '#';
                    $dataSub['text'] = isset($subitem['text']) ? $subitem['text'] : null;
                    $dataSub['icon'] = isset($subitem['icon']) ? $subitem['icon'] : null;
                    $dataSub['can'] = isset($subitem['can']) ? $subitem['can'] : null;
                    $supraData[] = $dataSub;
                }
                DB::table('setup_menu')->insert($supraData);
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
        Schema::dropIfExists('setup_menu');
    }

    public function getItems(): array
    {
        return
            [
                [
                    'text' => 'Finance Daybook',
                    'icon' => 'fa fa-table fa-fw',
                    'submenu' =>[
                        [
                            'text' => 'show_classifications',
                            'url' => '/finance_catalogue/show_classifications',
                            'icon' => 'fa fa-book fa-fw',
                        ],
                        [
                            'text' => 'Finances Catalogue',
                            'url' => '/finance_catalogue',
                            'icon' => 'fa fa-book fa-fw',
                        ],
                        [
                            'text' => 'Finances DayBook',
                            'url' => '/finance_daybook',
                            'icon' => 'fa fa-book fa-fw',
                        ],
                    ],
                ],
                [
                    'text' => 'Invoice Discount',
                    'icon' => 'fa fa-table fa-fw',
                    'submenu' => [
                        [
                            'text' => 'invoice_discount',
                            'url' => '/invoice_discount',
                            'icon' => 'fa fa-book fa-fw',
                        ],
                        [
                            'text' => 'export_invoice',
                            'url' => 'invoice_discount/export_invoice',
                            'icon' => 'fa fa-book fa-fw',
                            'can' => '',
                        ],
                    ],
                ],
                [
                    'text' => 'Clone Models',
                    'icon' => 'fa fa-table fa-fw',
                    'submenu' => [
                        [
                            'text' => 'clone_models',
                            'url' => '/clone_models',
                            'icon' => 'fa fa-book fa-fw',
                        ]
                    ],
                ],
            ];
    }

};
