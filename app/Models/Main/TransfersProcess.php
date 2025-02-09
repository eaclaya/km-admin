<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class TransfersProcess extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    
    public $timestamps = true;

    protected $table = 'transfers_process';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'account_id',
        'in_process',
    ];

    public function finishProcess(){
        $this->in_process = 0;
        $this->save();
        return;
    }
}
