<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ReportProcess;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReportExportClients extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $nameFile;

    protected $reportProcessId;

    protected $stores;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nameFile, $reportProcessId, $stores)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->stores = $stores;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nameFile = $this->nameFile;
        $reportProcessId = $this->reportProcessId;
        $stores = $this->stores;
        $exception = null;
        try {
            $file = public_path().'/'.$nameFile;
            $fp = fopen($file, 'a+');
            $clients = Client::whereIn('account_id', $stores)->orderBy('type')->get();
            $employees = Employee::whereIn('id', $clients->pluck('seller_id'))->select('id', \DB::raw("CONCAT(first_name, ' ', last_name) as name"))->pluck('name', 'id');
            $accounts = Account::whereIn('id', $stores)->pluck('name', 'id');
            foreach ($clients as $client) {
                $result = [
                    'id' => $client->id,
                    'name' => $client->company_name ? $client->company_name : $client->name,
                    'phone' => $client->work_phone ? $client->work_phone : $client->phone,
                    'id_number' => $client->id_number,
                    'type' => $client->type,
                    'address' => $client->address1,
                    'employee' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id] : '',
                    'points' => $client->points,
                    'total_history' => $client->paid_to_date,
                    'invoice_date' => $client->invoice_date,
                    'account' => isset($accounts[$client->account_id]) ? $accounts[$client->account_id] : '',
                    'created_at' => $client->created_at,
                ];
                fputcsv($fp, $result, ';');
            }
            fclose($fp);
        } catch (\Exception $e) {
            $exception = $e;
            dump($e);
        }
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int) $reportProcess->count_rows + 1;
        $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;
        if (! is_null($exception)) {
            $reportProcess->exception .= substr(trim($exception), 0, 200).'*--*';
            $reportProcess->status = 2;
        }
        if ($finish) {
            $updated_at = Carbon::now()->toDateTimeString();
            $reportProcess->updated_at = $updated_at;
            if ($reportProcess->status !== 2) {
                $reportProcess->status = 1;
            }
        }
        $reportProcess->save();

    }
}
