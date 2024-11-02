<?php

namespace App\Services;
use App\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use \Log;

class TrackingHistoryService
{
    public static function trackModelChanges($model, $user_id, $real_user_id, $reason)
    {
        $changes = $model->getDirty();
        $beforeData = [];
        $afterData = [];

        foreach ($changes as $column => $afterValue) {
            $beforeValue = $model->getOriginal($column);
            if($column == 'updated_at') {
                continue;
            }
            if($beforeValue == $afterValue) {
                continue;
            }
            $beforeData[$column] = $beforeValue;
            $afterData[$column] = $afterValue;
        }
        if (count($beforeData) == 0) {
            return;
        }
        $date = new \Datetime();
        $created_at = $date->format('Y-m-d H:i:s');

        DB::connection('main')->table('tracking_history')->insert([
            'reason' => $reason,
            'model' => $model->getEntityType(),
            'model_id' => $model->id,
            'before_data' => json_encode($beforeData),
            'after_data' => json_encode($afterData),
            'user_id' => $user_id,
            'real_user_id' => $real_user_id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);
    }

    public static function trackSetArrayChanges($model, $oldData, $newData, $columns=null)
    {
        if ($oldData instanceof \Illuminate\Support\Collection) {
            $oldData = $oldData->toArray();
        }
        if ($newData instanceof \Illuminate\Support\Collection) {
            $newData = $newData->toArray();
        }
        $beforeData = [];
        $afterData = [];
        foreach($oldData as $old){
            if (isset($columns)){
                foreach ($columns as $column) {
                    $beforeData[$column][] = $old[$column];
                }
            }else{
                $entityType = $model->getEntityType();
                $beforeData[$entityType][] = $old;
            }
        }
        foreach($newData as $new){
            if (isset($columns)){
                foreach ($columns as $column) {
                    $afterData[$column][] = $new[$column];
                }
            }else{
                $entityType = $model->getEntityType();
                $afterData[$entityType][] = $new;
            }
        }
        if (count($beforeData) == 0 && count($afterData) == 0) {
            return;
        }
        $user = Auth::user();
        $realUser = $user->realUser();
        $date = new \Datetime();
        $created_at = $date->format('Y-m-d H:i:s');

        DB::table('tracking_history')->insert([
            'model' => $model->getEntityType(),
            'model_id' => $model->id,
            'before_data' => json_encode($beforeData),
            'after_data' => json_encode($afterData),
            'user_id' => $user->id,
            'real_user_id' => $realUser->id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ]);
    }
}
