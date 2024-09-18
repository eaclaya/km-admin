<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon\Carbon;
use DB;
use Utils;

use App\Models\ReportProcess;
use App\Models\WhatsappErrors;



class ReportWhatsappErrors extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    protected $nameFile, $reportProcessId, $stores, $dateSerach;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nameFile, $reportProcessId, $stores, $dateSerach)
    {
        $this->nameFile = $nameFile;
        $this->reportProcessId = $reportProcessId;
        $this->stores = $stores;
        $this->dateSerach = $dateSerach;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \App::setLocale('es');
        // dump('inicio a procesar con la siguiente tienda');
        $nameFile = $this->nameFile;
        $reportProcessId = $this->reportProcessId;
        $stores = $this->stores;
        // dump($stores);
        $dateSerach = $this->dateSerach;
        // dump('con esta fecha: ');
        // dump($dateSerach);

        $exception = null;
        try{
            $whatsappErrors = WhatsappErrors::with('account')->whereIn('account_id', $stores)->whereDate('error_at','=',$dateSerach)->get();

            $file = public_path() . '/' . $nameFile;
            $fp = fopen($file, 'a+');
            foreach($whatsappErrors as $error){
                // dump('entro al error: '.$error->id);
                // dump('verificare el modelo: '.$error->model);
                $model = $error->getModel();
                if($error->model == ENTITY_CLIENT){
                    $model_id = (isset($model->name) && trim($model->name) !== '') ? trim($model->name) : ((isset($model->company_name) && trim($model->company_name) !== '') ? trim($model->company_name) : trim($model->contact_name));
                }else{
                    $model_id = $model->invoice_number;
                }
                // dump('obtuve el identificador: '.$model_id);
                $fields = [
                    'account' => $error->account->name,
                    'model' => trans("texts.$error->model"),
                    'id' => $model_id,
                    'attempts' => $error->attempts,
                    'errors' => $error->error,
                    'is_send' => (!$error->is_send) ? 'No' : 'si',
                    'created_at' => $error->created_at,
                    'updated_at' => $error->updated_at,
                ];
                fputcsv($fp, $fields, ';');
            }

            fclose($fp);
        }catch (\Exception $e){
            $exception = $e;
            dump($e);
        }
        
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int)$reportProcess->count_rows + 1;
        $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;
        if(!is_null($exception)){
            $reportProcess->exception .= substr(trim($exception),0,200) . '*--*';
            $reportProcess->status = 2;
        }
        if($finish){
            $updated_at = Carbon::now()->toDateTimeString();
            $reportProcess->updated_at = $updated_at;
            if($reportProcess->status !== 2){
                $reportProcess->status = 1;
            }
        }
        $reportProcess->save();
        return;
    }
}
