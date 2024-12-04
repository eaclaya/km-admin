<?php

namespace App\Listeners;

use App\Events\TrackingEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\TrackingHistoryService;
use \Log;

class TrackingListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TrackingEvent $event): void
    {
        $data = $event->data;

        $model = isset($data['model']) ? $data['model'] : null;
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;
        $real_user_id = isset($data['real_user_id']) ? $data['real_user_id'] : null;
        $reason = isset($data['reason']) ? $data['reason'] : null;

        TrackingHistoryService::trackModelChanges($model,$user_id,$real_user_id,$reason);
    }
}
