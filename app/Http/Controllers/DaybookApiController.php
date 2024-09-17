<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDaybookHook;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DaybookApiController extends Controller
{
    protected DaybookService $daybookService;
    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function hook(Request $request): bool
    {
        $input = $request->all();
        $input = ['type' => 'invoice_payment', 'models_id' => ['payment_id' => 273, 'invoice_id' => 380]];
        Log::info('Daybook Webhook Init', [$input]);
//        ProcessDaybookHook::dispatch($this->daybookService, $input);
        $this->daybookService->initProcess($input);
        return true;
    }
}
