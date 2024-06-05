<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CloningControl extends Model
{
    protected $table = 'cloning_control';

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
        'account_id' => 'integer',
        'is_completed' => 'boolean',
    ];

    protected $fillable = [
        'model',
        'account_id',
        'from_date',
        'to_date',
        'is_completed',
    ];

    public function account()
    {
        return DB::connection('main')->table('accounts')
            ->select('id','name')
            ->find($this->account_id);
    }

    public function getFirstOrNew($dataReset = null){
        if(isset($dataReset) && count($dataReset) > 0){
            $control = CloningControl::
                                where('model',$dataReset['model'])
                                ->where('account_id', $dataReset['accountId'])
                                ->where('is_completed', 0)
                                ->latest()->first();

            if(isset($control)){
                return $control;
            }else{
                $cloningControl = CloningControl::
                                where('model',$dataReset['model'])
                                ->where('account_id', $dataReset['accountId'])
                                ->latest()->first();
                if(isset($cloningControl)){
                    $fromDate = $cloningControl->to_date->addDay()->startOfDay();
                    $current_at = Carbon::now()->startOfDay();
                    if($fromDate->gte($current_at)){
                        return null;
                    }
                    $toDate = $fromDate->copy()->endOfMonth()->endOfDay();
                    $control = CloningControl::create([
                        'model' => $dataReset['model'],
                        'account_id' => $dataReset['accountId'],
                        'from_date' => $fromDate,
                        'to_date' => $toDate,
                        'is_completed' => 0,
                    ]);
                    return $control;
                }else{
                    $year = 2022;
                    $month = 1;
                    $firstDayOfMonth = Carbon::create($year, $month)->startOfMonth();
                    $fromDate = $firstDayOfMonth->startOfDay();

                    $lastDayOfMonth = Carbon::create($year, $month)->endOfMonth();
                    $toDate = $lastDayOfMonth->endOfDay();

                    $control = CloningControl::create([
                        'model' => $dataReset['model'],
                        'account_id' => $dataReset['accountId'],
                        'from_date' => $fromDate,
                        'to_date' => $toDate,
                        'is_completed' => 0,
                    ]);
                    return $control;
                }
            }
        }
        return false;
    }
}
