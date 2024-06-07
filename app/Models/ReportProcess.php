<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ReportProcess extends Model
{
    use SoftDeletes;

    protected $table = 'report_process';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'report',
        'file',
        'status',
    ];

    /**
     * @return mixed
     */
    public function getFile()
    {
        $file = public_path() . '/' . $this->file;
        return $file;
    }
}
