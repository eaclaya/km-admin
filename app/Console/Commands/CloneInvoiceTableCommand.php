<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\CloneInvoiceTableJob;
use App\Models\CloningControl;

class CloneInvoiceTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clone-invoices {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clone invoices table from main to mysql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $account_id = $this->argument('account_id') ? $this->argument('account_id') : null;
        if ($account_id) {
            $control = new CloningControl;
            $control = $control->getFirstOrNew([
                'model' => ENTITY_INVOICE,
                'accountId' => $account_id,
            ]);
            if(is_null($control)){
                return;
            }
            CloneInvoiceTableJob::dispatch($control->from_date, $control->to_date, $account_id, $control->id);
        }else{
            $accounts = DB::connection('main')->table('accounts')->pluck('id');
            $count = 0;
            foreach ($accounts as $accountId) {
                $control = new CloningControl;
                $control = $control->getFirstOrNew([
                    'model' => ENTITY_INVOICE,
                    'accountId' => $accountId,
                ]);
                if(is_null($control)){
                    return;
                }
                CloneInvoiceTableJob::dispatch($control->from_date, $control->to_date, $accountId, $control->id)->delay($count * 10);
                $count++;
            }
        }
        return;
    }
}
