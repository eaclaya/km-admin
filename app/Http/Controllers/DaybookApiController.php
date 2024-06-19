<?php

namespace App\Http\Controllers;

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
        Log::info('Whatsapp Webhook Init');
        $this->daybookService->selectAction($input);
        return true;
    }
}
