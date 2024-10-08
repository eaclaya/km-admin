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

use App\Models\Main\ReportProcess;
use App\Models\Main\WhatsappConfigAccount;



class ReportWhatsappDisconnected extends Job implements ShouldQueue, SelfHandling
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
        $stores = $this->stores;

        $whatsappConfigs = WhatsappConfigAccount::with('account')->whereIn('account_id', $stores)->get();

        $file = public_path() . '/' . $nameFile;
        $fp = fopen($file, 'a+');
        $error = false;
        $newStores = [];
		foreach($whatsappConfigs as $config){
            $result = '';
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ",$currentDate)[1];

            if($config->active_messages &&
                (isset($config->instance_id) && trim($config->instance_id) !== '') &&
                (isset($config->access_token) && trim($config->access_token) !== ''))
            {
                try {
                    $headers = [
                        'Content-Type' => 'application/json'
                    ];
                    $client = new \GuzzleHttp\Client([ 'headers' => $headers, 'timeout' => 4, 'connect_timeout' => 4]);
                    // https://socializerx.com/api/reconnect?instance_id=609ACF283XXXX&access_token=65ac077ef0c0a
                    $response = $client->request('GET', 'https://socializerx.com/api/reconnect?instance_id='.$config->instance_id.'&access_token='.$config->access_token);
                    if($response->getStatusCode() == 200){
                        $body = $response->getBody();
                        $result = $body;
                        $dataResponse = json_decode((string) $body, true);
                        if($dataResponse['status'] == "error"){
                            $result = trim($dataResponse['message']);
                            if($result !== 'ID de instancia no validada'){
                                $error = true;
                                $newStores[] = $config->account_id;
                            }
                        }
                    }else{
                        $result = 'estatus: '.$response->getStatusCode();
                        $error = true;
                        $newStores[] = $config->account_id;
                    }
                } catch (\Exception $e) {
                    $result = 'Error al momento de llamar la api: '.substr($e, 0, 250);
                    $error = true;
                    $newStores[] = $config->account_id;
                }
            } else {
                $result = 'no tiene activo o correctamente configurado el Whatsapp';
            }
                    
            $fields = [
                'account' => $config->account->name,
                'token' => $config->access_token,
                'instance' => $config->instance_id,
                'result' => $result,
                'hours' => $currentDate,
                'success' => (!$error) ? 'correcto' : 'error',
            ];
            fputcsv($fp, $fields, ';');
        }
        // dump('aca ya se lleno el archivo');
		fclose($fp);
        // dump('aca ya se cerro el archivo');
        if($error && count($newStores) > 0){
            $randNumber = rand(1, 120);
            dispatch((new ReportWhatsappDisconnected($nameFile, $reportProcessId, $newStores))->delay($randNumber));
        }else{
            $reportProcess = ReportProcess::find($reportProcessId);
            $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int)$reportProcess->count_rows + 1;
            $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;
            if($finish){
                $updated_at = Carbon::now()->toDateTimeString();
                $reportProcess->updated_at = $updated_at;
                $reportProcess->status = 1;
            }
            $reportProcess->save();
        }
        return;
    }
}
