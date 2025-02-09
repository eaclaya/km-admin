<?php

namespace App\Models\Main;
use Auth;

use Illuminate\Database\Eloquent\Model;

class ReferenceNumber extends ModelDBMain
{

    protected $connection = 'main';
    protected $table = 'reference_numbers';

    public static function createReferenceNumber($table, $tableId, $reference, $comments = 'Guardado automatico')
    {
        $referenceNumber = new self();
        $referenceNumber->table_name = $table;
        $referenceNumber->table_id = $tableId;
        $referenceNumber->reference = $reference;
        $referenceNumber->real_user_id = Auth::user()->realUser()->id;
        $referenceNumber->comments = $comments;
        $referenceNumber->save(); 

        return $referenceNumber->id;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Main\User', 'real_user_id');
    }
}
