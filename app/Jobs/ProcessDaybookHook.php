<?php

namespace App\Jobs;

use App\Services\DaybookService;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Carbon\Carbon;

class ProcessDaybookHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected DaybookService $daybookService;
    protected array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DaybookService $daybookService, array $data)
    {
        $this->daybookService = $daybookService;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
//        -------------------------
        $this->daybookService->initProcess($data);
        return;
    }

}
