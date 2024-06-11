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
        $data = $this->getItems();
        $isset = DB::table('setup_menu')->where('url', $data['url'])->first();
        if (!$isset) {
            DB::table('setup_menu')->insert($data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

    public function getItems(): array
    {
        return
        [
            'text' => 'Setup Menu',
            'url' => '/setup_menu',
            'icon' => 'fa fa-book fa-fw',
        ];
    }

};
