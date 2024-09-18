<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use App\Models\Account;
use App\Models\Product;
use App\Models\ReportProcess;
use Carbon\Carbon;
use DB;
use Mail;

class ReportStockInStores extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    protected $nameFile, $reportProcessId, $stores;

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
        $storeIds = $this->stores;

        $stores = Account::whereIn('id', $storeIds)->get();
        $today = Carbon::now();
        $exception = null;

        try {
            $file = public_path() . '/' . $nameFile;
            $fp = fopen($file, 'a+');

            foreach ($stores as $account) {
                    $valor = Product::where('account_id', $account->id)
                        ->where('qty', '>', 0)
                        ->selectRaw('SUM(wholesale_price * qty) as total_price, SUM(qty) as total_qty')
                        ->first();
                     $result = [
                            $account->name??'',
                            $account->organization_company->name??'',
                            $account->vat_number??'',
                            $valor->total_qty??0,
                            $valor->total_price??0,
                            $account->work_phone,
                            $account->address1. ' '.$account->address2,
                            $today->format('Y-m-d')
                        ];
                        fputcsv($fp, $result, ';');
                }
            fclose($fp);
        } catch (\Exception $e) {
            $exception = $e;
            dump($e);
        }

        // Actualiza el estado del proceso en la base de datos
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = 1;
        $finish = true;

        if (!is_null($exception)) {
            $reportProcess->exception .= substr(trim($exception), 0, 200) . '*--*';
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

        if (config('app.env') == 'production') {
            $emails[] = 'garridoosman@gmail.com';
            $emails[] = 'jdpfragoso@gmail.com';
        }else{
            $emails[] = 'jdpfragoso@gmail.com';
        }
        array_unique($emails);
        $nameFile = $reportProcess->file;
        $filePath = public_path() . "/" . $nameFile;

        try {
            Mail::send('emails.report_ventas', [], function ($message) use ($emails,$filePath, $nameFile) {
                $message->bcc($emails)
                    ->from('garridoosman@gmail.com', 'Osman Garrido')
                    ->subject('Reporte Total Inventario Tiendas Ventas')
                    ->attach($filePath, [
                        'as' => $nameFile,
                        'mime' => 'application/vnd.ms-excel'
                    ]);
            });
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
