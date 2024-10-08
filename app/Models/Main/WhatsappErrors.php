<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class WhatsappErrors extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'whatsapp_errors';

    /**
     * @var array
     */
    protected $fillable = [
        'event',
        'model',
        'model_id',
        'account_id',
        'attempts',
        'error',
        'error_at',
        'is_send',
        'client_id',
        'contact',
        'message',
        'verify_response'
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function getModel(){
        $class = "App\\Models\\" . ucwords($this->model);
        return $class::find($this->model_id);
        // return $this->belongsTo($model,'id','model_id');
    }
    public function saveFirstOrNew($dataReset = null){
        if(isset($dataReset) && count($dataReset) > 0){
            $date = new \Datetime();
            $created_at = $date->format('Y-m-d');
            $whatsappErrors = WhatsappErrors::where('event',$dataReset['event'])->where('model',$dataReset['model']->getEntityType())->where('model_id', $dataReset['model']->id)->first();
            if(isset($whatsappErrors)){
                if(isset($dataReset['error'])){
                    $whatsappErrors->attempts = $whatsappErrors->attempts + 1;
                    $whatsappErrors->error .= "\n \n".$whatsappErrors->attempts.': '.trim($dataReset['error']);
                    $whatsappErrors->error_at = $created_at;
                }else{
                    $whatsappErrors->attempts = $whatsappErrors->attempts + 1;
                    $whatsappErrors->is_send = 1;
                }
                $whatsappErrors->contact = isset($dataReset['contact']) ? trim($dataReset['contact']) : null;
                $whatsappErrors->message = isset($dataReset['message']) ? trim($dataReset['message']) : null;
                $whatsappErrors->save();
                return $whatsappErrors;
            }else{
                $this->event = $dataReset['event'];
                $this->model = $dataReset['model']->getEntityType();
                $this->model_id = $dataReset['model']->id;
                $this->account_id = $dataReset['model']->account_id;
                $this->attempts = 1;
                $this->error_at = $created_at;
                if(isset($dataReset['error'])){
                    $this->error = trim($dataReset['error']);
                    $this->is_send = false;
                }else if(isset($dataReset['success'])){
                    $this->error = trim($dataReset['success']);
                    $this->is_send = true;
                }
                $this->contact = isset($dataReset['contact']) ? trim($dataReset['contact']) : null;
                $this->message = isset($dataReset['message']) ? trim($dataReset['message']) : null;
                $this->save();
                return $this;
            }
        }
        return false;
    }
}
