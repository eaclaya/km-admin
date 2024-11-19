<?php

namespace App\Traits;

use App\Events\TrackingEvent;
use Illuminate\Support\Facades\Auth;

trait UpdatesWithEvent
{
    protected static $reason = '';

    protected static $is_tracking = false;

    public static function bootUpdatesWithEvent()
    {
        static::updated(function ($model) {
            if (self::$is_tracking == true) {

                $user_id = Auth::check() ? Auth::id() : (isset($model->user_id) ? $model->user_id : 1);
                $real_user_id = Auth::check() ? Auth::user()->realUser()->id : (isset($model->real_user_id) ? $model->real_user_id : $user_id);

                TrackingEvent::dispatch(['model' => $model, 'user_id' => $user_id, 'real_user_id' => $real_user_id, 'reason' => self::$reason]);
                self::$is_tracking = false;
            }
        });
    }

    public function setReason($reason)
    {
        self::$reason = $reason;
    }

    public function activateTracking()
    {
        self::$is_tracking = true;
    }
}
