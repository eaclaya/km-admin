<?php namespace App\Models\Main;

use Utils;
use DB;
use Carbon;
use Auth;
use Session;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Main\Invoice;
use App\Models\Main\Employee;

/* use App\Events\WhatsappClientWasCreated;
use App\Events\WhatsappClientWasUpdated; */

/**
 * Class Client
 */
class Client extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ClientPresenter';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'id_number',
        'vat_number',
        'work_phone',
        'custom_value1',
        'custom_value2',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'size_id',
        'industry_id',
        'currency_id',
        'language_id',
        'payment_terms',
        'website',
        'vouchers_discount',
        'amount_vouchers_kms',
        'percentage_vouchers',
        'refund_credit',
        'receive_messages',
        'birthday',
        'is_company',
        'type',
        'seller_id',
        'price_group',
        'maps_url',
        'points',
        'company_name',
        'user_id',
        'contact_name',
        'account_id',
        'phone',
        'route_name',
        'route_id',
        'frequency_id',
        'frequency_day',
        'is_credit',
        "longitude",
        "latitude",
        'limit_credit',
        'blocked_credit'
    ];

    /**
     * @var string
     */
    public static $fieldName = 'name';
    /**
     * @var string
     */
    public static $fieldPhone = 'work_phone';
    /**
     * @var string
     */
    public static $fieldAddress1 = 'address1';
    /**
     * @var string
     */
    public static $fieldAddress2 = 'address2';
    /**
     * @var string
     */
    public static $fieldCity = 'city';
    /**
     * @var string
     */
    public static $fieldState = 'state';
    /**
     * @var string
     */
    public static $fieldPostalCode = 'postal_code';
    /**
     * @var string
     */
    public static $fieldNotes = 'notes';
    /**
     * @var string
     */
    public static $fieldCountry = 'country';
    /**
     * @var string
     */
    public static $fieldWebsite = 'website';
    /**
     * @var string
     */
    public static $fieldVatNumber = 'vat_number';

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            Client::$fieldName,
            Client::$fieldPhone,
            Client::$fieldAddress1,
            Client::$fieldAddress2,
            Client::$fieldCity,
            Client::$fieldState,
            Client::$fieldPostalCode,
            Client::$fieldCountry,
            Client::$fieldNotes,
            Client::$fieldWebsite,
            Client::$fieldVatNumber,
            Contact::$fieldFirstName,
            Contact::$fieldLastName,
            Contact::$fieldPhone,
            Contact::$fieldEmail,
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'first' => 'first_name',
            'last' => 'last_name',
            'email' => 'email',
            'mobile|phone' => 'phone',
            'name|organization' => 'name',
            'street2|address2' => 'address2',
            'street|address|address1' => 'address1',
            'city' => 'city',
            'state|province' => 'state',
            'zip|postal|code' => 'postal_code',
            'country' => 'country',
            'note' => 'notes',
            'site|website' => 'website',
            'vat' => 'vat_number',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function frequency(){
		return $this->belongsTo('App\Models\Main\Frequency', 'frequency_id');
	}
    
	public function route_client()
	{
		return $this->hasMany(RouteClient::class, 'client_id');
	}

    public function clients_blocked_history()
	{
		return $this->hasMany(ClientsBlockedHistory::class, 'client_id');
	}

    public function seller(){
	return $this->belongsTo('App\Models\Main\Employee');
    }
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\Main\Invoice')->where('invoice_type_id', '=', INVOICE_TYPE_STANDARD);
    }

    public function visits(){
	return $this->hasMany('App\Models\Main\Visit')->orderBy('id', 'DESC');
    }

    public function client_visit($date=null){
		return $this->hasMany('App\Models\Main\Visit')->whereDate('created_at', '=', $date??date('Y-m-d'));
	}
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotes()
    {
        return $this->hasMany('App\Models\Main\Invoice')->where('invoice_type_id', '=', INVOICE_TYPE_QUOTE)->where('account_id', Auth::user()->account_id);
    }

    public function quotesFilteredByDate($startDate, $endDate)
    {

        return $this->hasMany('App\Models\Main\Invoice')->where('invoice_type_id', '=', INVOICE_TYPE_QUOTE)->where('invoice_status_id', INVOICE_STATUS_PAID)->where('invoice_date', '>=', $startDate)->where('invoice_date', '<=', $endDate)->orderBy('invoice_number');
    }


    public function invoicesFilteredByDate($startDate, $endDate)
    {

        return $this->hasMany('App\Models\Main\Invoice')->where('invoice_type_id', '=', INVOICE_TYPE_STANDARD)->where('invoice_date', '>=', $startDate)->where('invoice_date', '<=', $endDate)->orderBy('invoice_number');
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('App\Models\Main\Payment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany('App\Models\Main\Contact');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Main\Country');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Main\Currency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Models\Main\Language');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function size()
    {
        return $this->belongsTo('App\Models\Main\Size');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo('App\Models\Main\Industry');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function credits()
    {
        return $this->hasMany('App\Models\Main\Credit');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function creditsWithBalance()
    {
        return $this->hasMany('App\Models\Main\Credit')->where('balance', '>', 0);
    }

    /**
     * @return mixed
     */
    public function expenses()
    {
        return $this->hasMany('App\Models\Main\Expense','client_id','id')->withTrashed();
    }

    /**
     * @param $data
     * @param bool $isPrimary
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addContact($data, $isPrimary = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : (isset($data['id']) ? $data['id'] : false);

        if ($publicId && $publicId != '-1') {
            $contact = Contact::scope($publicId)->firstOrFail();
        } else {
            $contact = Contact::createNew();
            $contact->send_invoice = true;
        }

        if (Utils::hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $this->account->enable_portal_password){
            if(!empty($data['password']) && $data['password']!='-%unchanged%-'){
                $contact->password = bcrypt($data['password']);
            } else if(empty($data['password'])){
                $contact->password = null;
            }
        }
        $contact->fill($data);
        $contact->is_primary = $isPrimary;

        return $this->contacts()->save($contact);
    }

    /**
     * @param $balanceAdjustment
     * @param $paidToDateAdjustment
     */
    public function updateBalances($balanceAdjustment, $paidToDateAdjustment)
    {
        if ($balanceAdjustment === 0 && $paidToDateAdjustment === 0) {
            return;
        }
	$this->invoice_date = date('Y-m-d');
        $this->balance = $this->balance + $balanceAdjustment;
        $this->paid_to_date = $this->paid_to_date + $paidToDateAdjustment;

        $this->save();
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/clients/{$this->id}";
    }

    /**
     * @return float|int
     */
    public function getTotalCredit()
    {
        return DB::table('credits')
                ->where('client_id', '=', $this->id)
                ->whereNull('deleted_at')
                ->sum('balance');
    }

    public function getBalance(){
	return DB::table('invoices')
                ->where('client_id', '=', $this->id)
		->whereNull('deleted_at')
		->where('invoice_type_id', 1)
                ->sum('balance');
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPrimaryContact()
    {
        return $this->contacts()
                    ->whereIsPrimary(true)
                    ->first();
    }

    /**
     * @return mixed|string
     */
    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }

        if ( ! count($this->contacts)) {
            return '';
        }

        $contact = $this->contacts[0];

        return $contact->getDisplayName();
    }

    /**
     * @return string
     */
    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;
        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CLIENT;
    }

    /**
     * @return bool
     */
    public function hasAddress()
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        foreach ($fields as $field) {
            if ($this->$field) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getDateCreated()
    {
        if ($this->created_at == '0000-00-00 00:00:00') {
            return '---';
        } else {
            return $this->created_at->format('m/d/y h:i a');
        }
    }

    /**
     * @return bool
     */
    public function getGatewayToken()
    {
        $accountGateway = $this->account->getGatewayByType(GATEWAY_TYPE_TOKEN);

        if ( ! $accountGateway) {
            return false;
        }

        return AccountGatewayToken::clientAndGateway($this->id, $accountGateway->id)->first();
    }

    /**
     * @return bool
     */
    public function defaultPaymentMethod()
    {
        if ($token = $this->getGatewayToken()) {
            return $token->default_payment_method;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function autoBillLater()
    {
        if ($token = $this->getGatewayToken()) {
            if ($this->account->auto_bill_on_due_date) {
                return true;
            }

            return $token->autoBillLater();
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->balance + $this->paid_to_date;
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        if ($this->currency_id) {
            return $this->currency_id;
        }

        if (!$this->account) {
            $this->load('account');
        }

        return $this->account->currency_id ?: DEFAULT_CURRENCY;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        if ($this->currency) {
            return $this->currency->code;
        }

        if (!$this->account) {
            $this->load('account');
        }

        return $this->account->currency ? $this->account->currency->code : 'USD';
    }

    /**
     * @param $isQuote
     * @return mixed
     */
    public function getCounter($isQuote)
    {
        return $isQuote ? $this->quote_number_counter : $this->invoice_number_counter;
    }

    public function markLoggedIn()
    {
        $this->last_login = Carbon::now()->toDateTimeString();
        $this->save();
    }

    public function lastInvoice($complet = false){
        $invoice = Invoice::where('account_id', Auth::user()->account_id)->where('invoice_type_id', 1)->where('client_id', $this->id)->orderBy('invoice_date','DESC')->first();
        if($invoice){
            if($complet){
                return $invoice;
            }
            return $invoice->invoice_date;
        }
        return 'N/A';
    }

    public function employee(){
	$seller = Employee::find($this->seller_id);
        $name = $seller ? $seller->first_name." ".$seller->last_name : 'N/A';
        return $name;
    }

    public function is_client_route(){
        return Auth::user()->is_account_route($this->account_id??0);
    }


    /**
     * @return bool
     */
    public function hasAutoBillConfigurableInvoices(){
        return $this->invoices()->whereIn('auto_bill', [AUTO_BILL_OPT_IN, AUTO_BILL_OPT_OUT])->count() > 0;
    }

    public function generateMessage($type,$url_link)
    {
        $account = $this->account->name;
        $client = (isset($this->name) && trim($this->name) !== '') ? trim($this->name) : ((isset($this->company_name) && trim($this->company_name) !== '') ? trim($this->company_name) : trim($this->contact_name));
        $invoice = $this->lastInvoice(true);
        if($invoice !== 'N/A'){
            $model = ENTITY_PROFORMA;
            if ($invoice->invoice_type_id == INVOICE_TYPE_QUOTE) {
                $model = ENTITY_QUOTE;
            }
            if ($invoice->invoice_type_id == INVOICE_TYPE_STANDARD) {
                $model = str_contains(strtoupper($invoice->invoice_number), 'P') ? ENTITY_PROFORMA : ENTITY_INVOICE;
            }
            $model = trans("texts.$model");
        }else{
            $model = 'Cliente';
        }
        $rrss = "\n \n Síguenos en nuestras redes Sociales: https://www.kmmotos.com/pages/nuestras-redes-sociales";
        $footer = "\n Para desactivar las notificaciones envía la palabra clave: *dar_baja* (sin espacios y con el guion bajo intermedio) ".$rrss;
        $message = isset($this->account->whatsappConfigAccount->$type) ? $this->account->whatsappConfigAccount->$type : '';
        $message = str_replace('{model_invoice}', '*'.$model.'*', $message);
        $message = str_replace('{client}', '*'.$client.'*', $message);
        $message = str_replace('{account}', '*'.$account.'*', $message);
        $message = $message." \n ".$url_link." \n ".$footer;

        return $message;
    }
    
    public function getWhatsappConfig(){
        return $this->account->whatsappConfigAccount;
    }

    public function total_credit_client($id=null) {
        
		$total = Invoice::where('client_id', $id??$this->id)
			->where('is_credit', true)
			->where('invoice_status_id', '!=', 6)
			->where('invoice_status_id', '!=', 3)
			->where('invoice_type_id', '1')
			->where('amount', '>', '1')
			->where('account_id', '!=', '19')
			->groupBy('client_id')
			->selectRaw('client_id, 
				SUM(CASE WHEN in_transit = 1 THEN balance ELSE 0 END) as total_in_transit_balance,
				SUM(CASE WHEN in_transit = 0 THEN balance ELSE 0 END) as total_balance,
				MIN(end_date) as earliest_invoice_date,
				SUM(CASE WHEN end_date < CURDATE() THEN balance ELSE 0 END) as total_venced')
				->first();

		return $total;
	}

    public function invoices_venced($id=null) {
        
		$total = Invoice::where('client_id', $id??$this->id)
			->where('is_credit', true)
			->where('invoice_status_id', '!=', 6)
			->where('invoice_status_id', '!=', 3)
			->where('invoice_type_id', '1')
			->where('amount', '>', '1')
			->where('account_id', '!=', '19')
			->where('end_date', '<', Carbon::now())
            ->select('id','invoice_number', 'credit_days', 'invoice_date')
            ->get();

            foreach ($total as $invoice) {
                $total->payments = $invoice->payments;
            }
                
		return $total;
	}

    public function limit_exceeded(){
        $r = $this->total_credit_client($this->id);
        return $r? $this->limit_credit - $r->total_balance < 0: false;
    }
}

Client::creating(function ($client) {
    $client->setNullValues();
});

Client::created(function ($client) {
    /* if(!Session::get('updated_client_'.$client->id)){
        event(new WhatsappClientWasCreated($client));
    }
    Session::flash('updated_client_'.$client->id, 1); */
});

Client::updating(function ($client) {
    $client->setNullValues();
});