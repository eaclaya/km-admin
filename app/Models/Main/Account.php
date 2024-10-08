<?php namespace App\Models\Main;

use Eloquent;
use Utils;
use Session;
use DateTime;
use Event;
use Cache;
use Auth;
use App;
use DB;
use App\Events\UserSettingsChanged;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Main\Traits\PresentsInvoice;
use App\Models\Main\OrderRequest;
use App\Models\Main\ProductRequest;
use App\Models\Main\Supply;
use App\Models\Main\Refund;
use App\Models\Main\Cart;
use App\Models\Main\Vendor;
use App\Models\Main\FinanceAccount;
/**
 * Class Account
 */
class Account extends ModelDBMain
{

    protected $connection = 'main';

    use SoftDeletes;
    use PresentsInvoice;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\AccountPresenter';

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $hidden = ['ip'];

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'id_number',
        'vat_number',
        'work_email',
        'website',
        'work_phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'size_id',
        'industry_id',
        'email_footer',
        'timezone_id',
        'date_format_id',
        'datetime_format_id',
        'currency_id',
        'language_id',
        'military_time',
        'invoice_taxes',
        'invoice_item_taxes',
        'show_item_taxes',
        'default_tax_rate_id',
        'enable_second_tax_rate',
        'include_item_taxes_inline',
        'start_of_week',
        'financial_year_start',
        'enable_client_portal',
        'enable_client_portal_dashboard',
        'enable_portal_password',
        'send_portal_password',
        'enable_buy_now_buttons',
        'show_accept_invoice_terms',
        'show_accept_quote_terms',
        'require_invoice_signature',
        'require_quote_signature',
        'fast_pos',
        'change_product_price',
        'show_billing_in_invoice',
        'Matrix_address',
        'Matrix_name',
        'company_zones_id',
        'organization_company_id'
    ];

    /**
     * @var array
     */
    public static $basicSettings = [
        ACCOUNT_COMPANY_DETAILS,
        ACCOUNT_USER_DETAILS,
        ACCOUNT_LOCALIZATION,
        //ACCOUNT_PAYMENTS,
        ACCOUNT_TAX_RATES,
        //ACCOUNT_PRODUCTS,
        //ACCOUNT_NOTIFICATIONS,
        ACCOUNT_IMPORT_EXPORT,
        //ACCOUNT_MANAGEMENT,
    ];

    /**
     * @var array
     */
    public static $advancedSettings = [
        ACCOUNT_BILLING,
        ACCOUNT_REFUND,
        ACCOUNT_INVOICE_SETTINGS,
        //ACCOUNT_INVOICE_DESIGN,
        //ACCOUNT_EMAIL_SETTINGS,
        //ACCOUNT_TEMPLATES_AND_REMINDERS,
        //ACCOUNT_BANKS,
        //ACCOUNT_CLIENT_PORTAL,
        ACCOUNT_REPORTS,
        //ACCOUNT_DATA_VISUALIZATIONS,
        //ACCOUNT_API_TOKENS,
        ACCOUNT_USER_MANAGEMENT,
        ACCOUNT_AREAS_AND_ZONES,
        ACCOUNT_ORGANIZATION_COMPANY,
        ACCOUNT_SET_TOKEN
    ];

    public static $modules = [
        ENTITY_RECURRING_INVOICE => 1,
        ENTITY_CREDIT => 2,
        ENTITY_QUOTE => 4,
        ENTITY_TASK => 8,
        ENTITY_EXPENSE => 16,
        ENTITY_VENDOR => 32,
    ];

    public static $dashboardSections = [
        'total_revenue' => 1,
        'average_invoice' => 2,
        'outstanding' => 4,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function account_tokens()
    {
        return $this->hasMany('App\Models\Main\AccountToken');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\Main\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function travel_expenses()
    {
        return $this->hasMany('App\Models\Main\TravelExpenses');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Main\Order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany('App\Models\Main\Client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany('App\Models\Main\Contact');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\Main\Invoice');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function account_gateways()
    {
        return $this->hasMany('App\Models\Main\AccountGateway');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bank_accounts()
    {
        return $this->hasMany('App\Models\Main\BankAccount');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tax_rates()
    {
        return $this->hasMany('App\Models\Main\TaxRate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Models\Main\Product');
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
    public function timezone()
    {
        return $this->belongsTo('App\Models\Main\Timezone');
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
    public function date_format()
    {
        return $this->belongsTo('App\Models\Main\DateFormat');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function datetime_format()
    {
        return $this->belongsTo('App\Models\Main\DatetimeFormat');
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
    public function currency()
    {
        return $this->belongsTo('App\Models\Main\Currency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo('App\Models\Main\Industry');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_tax_rate()
    {
        return $this->belongsTo('App\Models\Main\TaxRate');
    }

    /**
     * @return mixed
     */
    public function expenses()
    {
        return $this->hasMany('App\Models\Main\Expense','account_id','id')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function payments()
    {
        return $this->hasMany('App\Models\Main\Payment','account_id','id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Main\Company');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo('App\Models\Main\CompanyZones', 'company_zones_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization_company()
    {
        return $this->belongsTo('App\Models\Main\OrganizationCompany');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function whatsappConfigAccount()
    {
        return $this->belongsTo('App\Models\Main\WhatsappConfigAccount', 'id', 'account_id');
    }

    /**
     * @return mixed
     */
    public function expense_categories()
    {
        return $this->hasMany('App\Models\Main\ExpenseCategory','account_id','id')->withTrashed();
    }

    /**
     * @param $value
     */
    public function setIndustryIdAttribute($value)
    {
        $this->attributes['industry_id'] = $value ?: null;
    }

    /**
     * @param $value
     */
    public function setCountryIdAttribute($value)
    {
        $this->attributes['country_id'] = $value ?: null;
    }

    /**
     * @param $value
     */
    public function setSizeIdAttribute($value)
    {
        $this->attributes['size_id'] = $value ?: null;
    }

    /**
     * @param int $gatewayId
     * @return bool
     */
    public function isGatewayConfigured($gatewayId = 0)
    {
        if ( ! $this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        if ($gatewayId) {
            return $this->getGatewayConfig($gatewayId) != false;
        } else {
            return count($this->account_gateways) > 0;
        }
    }

    /**
     * @return bool
     */
    public function isEnglish()
    {
        return !$this->language_id || $this->language_id == DEFAULT_LANGUAGE;
    }

    /**
     * @return bool
     */
    public function hasInvoicePrefix()
    {
        if ( ! $this->invoice_number_prefix && ! $this->quote_number_prefix) {
            return false;
        }

        return $this->invoice_number_prefix != $this->quote_number_prefix;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
   
        if ($this->name) {
            return $this->name;
        }

        //$this->load('users');
        $user = $this->users()->first();
        
        return $user->getDisplayName();
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
    public function getMomentDateTimeFormat()
    {
        $format = $this->datetime_format ? $this->datetime_format->format_moment : DEFAULT_DATETIME_MOMENT_FORMAT;

        if ($this->military_time) {
            $format = str_replace('h:mm:ss a', 'H:mm:ss', $format);
        }

        return $format;
    }

    /**
     * @return string
     */
    public function getMomentDateFormat()
    {
        $format = $this->getMomentDateTimeFormat();
        $format = str_replace('h:mm:ss a', '', $format);
        $format = str_replace('H:mm:ss', '', $format);

        return trim($format);
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        if ($this->timezone) {
            return $this->timezone->name;
        } else {
            return 'US/Eastern';
        }
    }

    public function getDate($date = 'now')
    {
        if ( ! $date) {
            return null;
        } elseif ( ! $date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        return $date;
    }

    /**
     * @param string $date
     * @return DateTime|null|string
     */
    public function getDateTime($date = 'now')
    {
        $date = $this->getDate($date);
        $date->setTimeZone(new \DateTimeZone($this->getTimezone()));

        return $date;
    }

    /**
     * @return mixed
     */
    public function getCustomDateFormat()
    {
        return $this->date_format ? $this->date_format->format : DEFAULT_DATE_FORMAT;
    }

    /**
     * @param $amount
     * @param null $client
     * @param bool $hideSymbol
     * @return string
     */
    public function formatMoney($amount, $client = null, $decorator = false)
    {
        if ($client && $client->currency_id) {
            $currencyId = $client->currency_id;
        } elseif ($this->currency_id) {
            $currencyId = $this->currency_id;
        } else {
            $currencyId = DEFAULT_CURRENCY;
        }

        if ($client && $client->country_id) {
            $countryId = $client->country_id;
        } elseif ($this->country_id) {
            $countryId = $this->country_id;
        } else {
            $countryId = false;
        }

        if ( ! $decorator) {
            $decorator = $this->show_currency_code ? CURRENCY_DECORATOR_CODE : CURRENCY_DECORATOR_SYMBOL;
        }

        return Utils::formatMoney($amount, $currencyId, $countryId, $decorator);
    }


    public function removeFormatMoney($amount)
    {
        return preg_replace("/[^0-9\.]/", '', $amount);
    }


    

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currency_id ?: DEFAULT_CURRENCY;
    }

    /**
     * @param $date
     * @return null|string
     */
    public function formatDate($date)
    {
        $date = $this->getDate($date);

        if ( ! $date) {
            return null;
        }

        return $date->format($this->getCustomDateFormat());
    }

    /**
     * @param $date
     * @return null|string
     */
    public function formatDateTime($date)
    {
        $date = $this->getDateTime($date);

        if ( ! $date) {
            return null;
        }

        return $date->format($this->getCustomDateTimeFormat());
    }

    /**
     * @param $date
     * @return null|string
     */
    public function formatTime($date)
    {
        $date = $this->getDateTime($date);

        if ( ! $date) {
            return null;
        }

        return $date->format($this->getCustomTimeFormat());
    }

    /**
     * @return string
     */
    public function getCustomTimeFormat()
    {
        return $this->military_time ? 'H:i' : 'g:i a';
    }

    /**
     * @return mixed
     */
    public function getCustomDateTimeFormat()
    {
        $format = $this->datetime_format ? $this->datetime_format->format : DEFAULT_DATETIME_FORMAT;

        if ($this->military_time) {
            $format = str_replace('g:i a', 'H:i', $format);
        }

        return $format;
    }

    /*
    public function defaultGatewayType()
    {
        $accountGateway = $this->account_gateways[0];
        $paymentDriver = $accountGateway->paymentDriver();

        return $paymentDriver->gatewayTypes()[0];
    }
    */

    /**
     * @param bool $type
     * @return AccountGateway|bool
     */
    public function getGatewayByType($type = false)
    {
        if ( ! $this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        /** @var AccountGateway $accountGateway */
        foreach ($this->account_gateways as $accountGateway) {
            if ( ! $type) {
                return $accountGateway;
            }

            $paymentDriver = $accountGateway->paymentDriver();

            if ($paymentDriver->handles($type)) {
                return $accountGateway;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function availableGatewaysIds()
    {
        if ( ! $this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        $gatewayTypes = [];
        $gatewayIds = [];

        foreach ($this->account_gateways as $accountGateway) {
            $paymentDriver = $accountGateway->paymentDriver();
            $gatewayTypes = array_unique(array_merge($gatewayTypes, $paymentDriver->gatewayTypes()));
        }

        foreach (Cache::get('gateways') as $gateway) {
            $paymentDriverClass = AccountGateway::paymentDriverClass($gateway->provider);
            $paymentDriver = new $paymentDriverClass();
            $available = true;

            foreach ($gatewayTypes as $type) {
                if ($paymentDriver->handles($type)) {
                    $available = false;
                    break;
                }
            }
            if ($available) {
                $gatewayIds[] = $gateway->id;
            }
        }

        return $gatewayIds;
    }

    /**
     * @param bool $invitation
     * @param mixed $gatewayTypeId
     * @return bool
     */
    public function paymentDriver($invitation = false, $gatewayTypeId = false)
    {
        /** @var AccountGateway $accountGateway */
        if ($accountGateway = $this->getGatewayByType($gatewayTypeId)) {
            return $accountGateway->paymentDriver($invitation, $gatewayTypeId);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function gatewayIds()
    {
        return $this->account_gateways()->pluck('gateway_id')->toArray();
    }

    /**
     * @param $gatewayId
     * @return bool
     */
    public function hasGatewayId($gatewayId)
    {
        return in_array($gatewayId, $this->gatewayIds());
    }

    /**
     * @param $gatewayId
     * @return bool
     */
    public function getGatewayConfig($gatewayId)
    {
        foreach ($this->account_gateways as $gateway) {
            if ($gateway->gateway_id == $gatewayId) {
                return $gateway;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasLogo()
    {
        if($this->logo == ''){
            $this->calculateLogoDetails();
        }

        return !empty($this->logo);
    }

    /**
     * @return mixed
     */
    public function getLogoDisk(){
        return Storage::disk(env('LOGO_FILESYSTEM', 'logos'));
    }

    protected function calculateLogoDetails(){
        $disk = $this->getLogoDisk();

        if($disk->exists($this->account_key.'.png')){
            $this->logo = $this->account_key.'.png';
        } else if($disk->exists($this->account_key.'.jpg')) {
            $this->logo = $this->account_key.'.jpg';
        }

        if(!empty($this->logo)){
            $image = imagecreatefromstring($disk->get($this->logo));
            $this->logo_width = imagesx($image);
            $this->logo_height = imagesy($image);
            $this->logo_size = $disk->size($this->logo);
        } else {
            $this->logo = null;
        }
        $this->save();
    }

    /**
     * @return null
     */
    public function getLogoRaw(){
        if(!$this->hasLogo()){
            return null;
        }

        $disk = $this->getLogoDisk();
        return $disk->get($this->logo);
    }

    /**
     * @param bool $cachebuster
     * @return null|string
     */
    public function getLogoURL($cachebuster = false)
    {
        if(!$this->hasLogo()){
            return null;
        }

        $disk = $this->getLogoDisk();
        $adapter = $disk->getAdapter();

        if($adapter instanceof \League\Flysystem\Adapter\Local) {
            // Stored locally
            $logoUrl = url('/logo/' . $this->logo);

            if ($cachebuster) {
                $logoUrl .= '?no_cache='.time();
            }

            return $logoUrl;
        }

        return Document::getDirectFileUrl($this->logo, $this->getLogoDisk());
    }

    public function getLogoPath()
    {
        if ( ! $this->hasLogo()){
            return null;
        }

        $disk = $this->getLogoDisk();
        $adapter = $disk->getAdapter();

        if ($adapter instanceof \League\Flysystem\Adapter\Local) {
            return $adapter->applyPathPrefix($this->logo);
        } else {
            return Document::getDirectFileUrl($this->logo, $this->getLogoDisk());
        }
    }

    /**
     * @return mixed
     */
    public function getPrimaryUser()
    {
        return $this->users()
                    ->orderBy('id')
                    ->first();
    }

    /**
     * @param $userId
     * @param $name
     * @return null
     */
    public function getToken($userId, $name)
    {
        foreach ($this->account_tokens as $token) {
            if ($token->user_id == $userId && $token->name === $name) {
                return $token->token;
            }
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getLogoWidth()
    {
        if(!$this->hasLogo()){
            return null;
        }

        return $this->logo_width;
    }

    /**
     * @return mixed|null
     */
    public function getLogoHeight()
    {
        if(!$this->hasLogo()){
            return null;
        }

        return $this->logo_height;
    }

    /**
     * @param $entityType
     * @param null $clientId
     * @return mixed
     */
    public function createInvoice($entityType = ENTITY_INVOICE, $clientId = null, $taxRate = null)
    {
        $invoice = Invoice::createNew();
        $invoice->is_recurring = false;
        $invoice->tax_rate1 = $taxRate;
        $invoice->invoice_type_id = INVOICE_TYPE_STANDARD;
        $invoice->invoice_date = Utils::today();
        $invoice->start_date = Utils::today();
        $invoice->invoice_design_id = $this->invoice_design_id;
        $invoice->client_id = $clientId;
        
        $billing = \App\Models\Main\Billing::where('is_invoice', 1)->where('account_id', Auth::user()->account_id)->orderBy('billing_id', 'desc')->first();
        if($billing)
        {
            $invoice->billing_id = $billing->billing_id;
        }


        if ($entityType === ENTITY_RECURRING_INVOICE) {
            $invoice->invoice_number = microtime(true);
            $invoice->is_recurring = true;
        } else {
            if ($entityType == ENTITY_QUOTE) {
                $invoice->invoice_type_id = INVOICE_TYPE_QUOTE;
            }

            if ($this->hasClientNumberPattern($invoice) && !$clientId) {
                // do nothing, we don't yet know the value
            } elseif ( ! $invoice->invoice_number) {
                $invoice->invoice_number = $this->getNextInvoiceNumber($invoice);
            }
        }

        if (!$clientId) {
            $invoice->client = Client::createNew();
            $invoice->client->public_id = 0;
        }
        
        return $invoice;
    }

    /**
     * @param $invoice_type_id
     * @return string
     */
    public function getNumberPrefix($invoice_type_id)
    {
        if ( ! $this->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            return '';
        }

        return ($invoice_type_id == INVOICE_TYPE_QUOTE ? $this->quote_number_prefix : $this->invoice_number_prefix) ?: '';
    }

    /**
     * @param $invoice_type_id
     * @return bool
     */
    public function hasNumberPattern($invoice_type_id)
    {
        if ( ! $this->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            return false;
        }

        return $invoice_type_id == INVOICE_TYPE_QUOTE ? ($this->quote_number_pattern ? true : false) : ($this->invoice_number_pattern ? true : false);
    }

    /**
     * @param $invoice
     * @return string
     */
    public function hasClientNumberPattern($invoice)
    {
        $pattern = $invoice->invoice_type_id == INVOICE_TYPE_QUOTE ? $this->quote_number_pattern : $this->invoice_number_pattern;

        return strstr($pattern, '$custom');
    }

    /**
     * @param $invoice
     * @return bool|mixed
     */
    public function getNumberPattern($invoice_type_id)
    {
        $pattern = $invoice_type_id == INVOICE_TYPE_QUOTE ? $this->quote_number_pattern : $this->invoice_number_pattern;

        if (!$pattern) {
            return false;
        }

        $search = ['{$year}'];
        $replace = [date('Y')];

        $search[] = '{$counter}';
        $replace[] = str_pad($this->getCounter($invoice->invoice_type_id), $this->invoice_number_padding, '0', STR_PAD_LEFT);

        if (strstr($pattern, '{$userId}')) {
            $search[] = '{$userId}';
            $replace[] = str_pad(($invoice->user->public_id + 1), 2, '0', STR_PAD_LEFT);
        }

        $matches = false;
        preg_match('/{\$date:(.*?)}/', $pattern, $matches);
        if (count($matches) > 1) {
            $format = $matches[1];
            $search[] = $matches[0];
            $replace[] = str_replace($format, date($format), $matches[1]);
        }

        $pattern = str_replace($search, $replace, $pattern);

        if ($invoice->client_id) {
            $pattern = $this->getClientInvoiceNumber($pattern, $invoice);
        }

        return $pattern;
    }

    /**
     * @param $pattern
     * @param $invoice
     * @return mixed
     */
    private function getClientInvoiceNumber($pattern, $invoice)
    {
        if (!$invoice->client) {
            return $pattern;
        }

        $search = [
            '{$custom1}',
            '{$custom2}',
        ];

        $replace = [
            $invoice->client->custom_value1,
            $invoice->client->custom_value2,
        ];

        return str_replace($search, $replace, $pattern);
    }

    /**
     * @param $invoice_type_id
     * @return mixed
     */
    public function getCounter($invoice_type_id)
    {
        
        return $invoice_type_id == INVOICE_TYPE_QUOTE && !$this->share_counter ? $this->quote_number_counter : $this->invoice_number_counter;
    }

    /**
     * @param $entityType
     * @return mixed|string
     */
    public function previewNextInvoiceNumber($entityType = ENTITY_INVOICE)
    {

        $invoice = $this->createInvoice($entityType);
        return $this->getNextInvoiceNumber($invoice);
    }

   public function isCaiActive()
   {
	$billing = Billing::where('is_invoice', 1)->where('account_id', Auth::user()->account_id)->orderBy('billing_id', 'desc')->first();
	if($billing  && $billing->cai != "")
	   	return true;
	
	else
		return false;
	
   }

     public function getNextSupplyNumber(){
        $supply = Supply::withTrashed()->where('account_id', Auth::user()->account_id)->orderBy('public_id', 'DESC')->first();
        $counter = $supply ?  $supply->public_id + 1 : 1;
        return "S-".str_pad($counter, 8, '0', STR_PAD_LEFT);
     }

    public function getNextRefundNumber($refund = null){
	$account = $refund ? $refund->account : Auth::user()->account;
	if($account->refund_number_prefix){
		return $account->refund_number_prefix.str_pad($account->refund_number_counter, 8, '0', STR_PAD_LEFT);
	}
        $refund = Refund::where('account_id', $account->id)->orderBy('public_id', 'DESC')->first();
        $counter = $refund ?  $refund->public_id + 1 : 1;
        return "R-".str_pad($counter, 8, '0', STR_PAD_LEFT);
    }

    public function incrementRefundCounter()
    {
        if($this->refund_number_prefix){
            $this->refund_number_counter += 1;
            $this->save();
        }
    }

    public function getNextCartNumber(){
    $cart = Cart::where('account_id', Auth::user()->account_id)->orderBy('public_id', 'DESC')->first();
    $counter = $cart ?  $cart->public_id + 1 : 1;
    return "R-".str_pad($counter, 8, '0', STR_PAD_LEFT);
    }
    /**
     * @param $invoice
     * @param bool $validateUnique
     * @return mixed|string
     */
    // new code: make $validateUnique = false for pass unique validation
    public function getNextInvoiceNumber($invoice, $validateUnique = false)
    {
        //$invoice_type_id = $invoice->invoice_type_id == INVOICE_TYPE_STANDARD && ($invoice->tax_rate1 > 0 || $invoice->tax_rate1 < 0) ? INVOICE_TYPE_STANDARD : INVOICE_TYPE_QUOTE;
        $invoice_type_id = $invoice->invoice_type_id == INVOICE_TYPE_STANDARD ? INVOICE_TYPE_STANDARD : INVOICE_TYPE_QUOTE;
        if ($this->hasNumberPattern($invoice_type_id)) {
		$number = $this->getNumberPattern($invoice_type_id);
        } else {
        $counter = $this->getCounter($invoice_type_id);
        $prefix = $this->getNumberPrefix($invoice_type_id);
        // NEW CODE: CHECK INVOICE NUMBER ONLY IF INVOICE TYPE IS STANDARD
        if( $invoice_type_id == INVOICE_TYPE_STANDARD && $this->isCaiActive())
        {
            $counter = $this->checkInvoiceNumber($counter);
            if(!$counter) $counter = 0;
        }


        $counterOffset = 0;
        $check = false;

        $content = $counter;
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/myText.txt","wb");
        fwrite($fp,$content);
        fclose($fp);

            // confirm the invoice number isn't already taken
            do {
                $number = $prefix . str_pad($counter, $this->invoice_number_padding, '0', STR_PAD_LEFT);

                if ($validateUnique) {
                    $check = Invoice::scope(false, $this->id)->whereInvoiceNumber($number)->withTrashed()->first();
                    $counter++;
                    $counterOffset++;
                }
            } while ($check);

            // update the invoice counter to be caught up
            if ($counterOffset > 1) {
                if ($invoice_type_id == INVOICE_TYPE_QUOTE  && !$this->share_counter) {
                    $this->quote_number_counter += $counterOffset - 1;
                } else {
                    $this->invoice_number_counter += $counterOffset - 1;
                }

                $this->save();
            }
        }

        if ($invoice->recurring_invoice_id) {
            $number = $this->recurring_invoice_number_prefix . $number;
        }
        return $number;
    }

    public function checkInvoiceNumber($counter)
    {
        $billing = Billing::where('is_invoice', 1)->where('account_id', Auth::user()->account_id)->orderBy('billing_id', 'desc')->take(1)->first();
        $lastInvoice = Invoice::where('billing_id', $billing['billing_id'])->orderBy('id', 'desc')->orderBy('created_at', 'desc')->first();
        
        if(!$billing)
        {
            return $counter;
        }
        elseif($lastInvoice)
	{
	    $lastInvoiceNumberArray = explode('-',$lastInvoice['invoice_number']);
	    $lastInvoiceNumber = intval($lastInvoice['invoice_number']);
            //$lastInvoiceNumber = intval($lastInvoiceNumberArray[count($lastInvoiceNumberArray) - 1]); 
            $nextInvoiceNumber = $lastInvoiceNumber + 1;
	    if($_SERVER['REMOTE_ADDR'] == '131.161.55.4'){ mail('erick@mottandbow.com', 'KM MOTO LAST INVOICE', $lastInvoiceNumberArray[count($lastInvoiceNumberArray) - 1]); }
            $array = explode("-", $billing->to_invoice);
            $invoiceNumberLimit = intval($array[count($array) - 1]);

            if($counter > $invoiceNumberLimit || time() > strtotime($billing->limit_date." +1 days"))
	    {
		return $counter;
                return false;
            }
            else
            {
                return $counter;
            }

        }
        else
        {
            $array = explode("-", $billing->from_invoice);
            $newInvoiceNumber = intval($array[count($array) - 1]);
            
            return $newInvoiceNumber;    
        }
        
    }

    /**
     * @param $invoice
     */
    public function incrementCounter($invoice)
    {
	//$invoice_type_id = $invoice->invoice_type_id == INVOICE_TYPE_STANDARD && ($invoice->tax_rate1 > 0 || $invoice->tax_rate1 < 0) ? INVOICE_TYPE_STANDARD : INVOICE_TYPE_QUOTE;
	$invoice_type_id = $invoice->invoice_type_id == INVOICE_TYPE_STANDARD ? INVOICE_TYPE_STANDARD : INVOICE_TYPE_QUOTE;
        if ($invoice->invoice_number != $this->getNextInvoiceNumber($invoice, false)) {
            return;
        }
        if ($invoice_type_id == INVOICE_TYPE_QUOTE && !$this->share_counter) {
            $this->quote_number_counter += 1;
        } else {
		$this->invoice_number_counter += 1;
		if($this->shared_account_billing){
            $sharedAccountBilling = $this->shared_account_billing;
			$accounts = explode(',', $sharedAccountBilling);
			foreach($accounts as $accountId){
				DB::table('accounts')->where('id', $accountId)->update(['invoice_number_counter' => $this->invoice_number_counter]);
			}
		}
		if($this->external_billing){
			DB::connection('remote')->select("update accounts set invoice_number_counter = {$this->invoice_number_counter} where external_billing = 1;");
		}
        }
        $this->save();
    }

    /**
     * @param bool $client
     */
    public function loadLocalizationSettings($client = false)
    {
        $this->load('timezone', 'date_format', 'datetime_format', 'language');

        $timezone = $this->timezone ? $this->timezone->name : DEFAULT_TIMEZONE;
        Session::put(SESSION_TIMEZONE, $timezone);

        Session::put(SESSION_DATE_FORMAT, $this->date_format ? $this->date_format->format : DEFAULT_DATE_FORMAT);
        Session::put(SESSION_DATE_PICKER_FORMAT, $this->date_format ? $this->date_format->picker_format : DEFAULT_DATE_PICKER_FORMAT);

        $currencyId = ($client && $client->currency_id) ? $client->currency_id : ($this->currency_id ?: DEFAULT_CURRENCY);
        $locale = ($client && $client->language_id) ? $client->language->locale : (($this->language_id) ? $this->Language->locale : DEFAULT_LOCALE);

        Session::put(SESSION_CURRENCY, $currencyId);
        Session::put(SESSION_CURRENCY_DECORATOR, $this->show_currency_code ? CURRENCY_DECORATOR_CODE : CURRENCY_DECORATOR_SYMBOL);
        Session::put(SESSION_LOCALE, $locale);

        App::setLocale($locale);

        $format = $this->datetime_format ? $this->datetime_format->format : DEFAULT_DATETIME_FORMAT;
        if ($this->military_time) {
            $format = str_replace('g:i a', 'H:i', $format);
        }
        Session::put(SESSION_DATETIME_FORMAT, $format);

        Session::put('start_of_week', $this->start_of_week);
    }

    /**
     * @return bool
     */
    public function isNinjaAccount()
    {
        return $this->account_key === NINJA_ACCOUNT_KEY;
    }

    /**
     * @param $plan
     */
    public function startTrial($plan)
    {
        if ( ! Utils::isNinja()) {
            return;
        }

        $this->company->trial_plan = $plan;
        $this->company->trial_started = date_create()->format('Y-m-d');
        $this->company->save();
    }

    /**
     * @param $feature
     * @return bool
     */
    public function hasFeature($feature)
    {
        if (Utils::isNinjaDev()) {
            return true;
        }

        $planDetails = $this->getPlanDetails();
        $selfHost = !Utils::isNinjaProd();

        if (!$selfHost && function_exists('ninja_account_features')) {
            $result = ninja_account_features($this, $feature);

            if ($result != null) {
                return $result;
            }
        }

        switch ($feature) {
            // Pro
            case FEATURE_TASKS:
            case FEATURE_EXPENSES:
                if (Utils::isNinja() && $this->company_id < EXTRAS_GRANDFATHER_COMPANY_ID) {
                    return true;
                }

            case FEATURE_CUSTOMIZE_INVOICE_DESIGN:
            case FEATURE_DIFFERENT_DESIGNS:
            case FEATURE_EMAIL_TEMPLATES_REMINDERS:
            case FEATURE_INVOICE_SETTINGS:
            case FEATURE_CUSTOM_EMAILS:
            case FEATURE_PDF_ATTACHMENT:
            case FEATURE_MORE_INVOICE_DESIGNS:
            case FEATURE_QUOTES:
            case FEATURE_REPORTS:
            case FEATURE_BUY_NOW_BUTTONS:
            case FEATURE_API:
            case FEATURE_CLIENT_PORTAL_PASSWORD:
            case FEATURE_CUSTOM_URL:
                return $selfHost || !empty($planDetails);

            // Pro; No trial allowed, unless they're trialing enterprise with an active pro plan
            case FEATURE_MORE_CLIENTS:
                return $selfHost || !empty($planDetails) && (!$planDetails['trial'] || !empty($this->getPlanDetails(false, false)));

            // White Label
            case FEATURE_WHITE_LABEL:
                if ($this->isNinjaAccount() || (!$selfHost && $planDetails && !$planDetails['expires'])) {
                    return false;
                }
                // Fallthrough
            case FEATURE_CLIENT_PORTAL_CSS:
            case FEATURE_REMOVE_CREATED_BY:
                return !empty($planDetails);// A plan is required even for self-hosted users

            // Enterprise; No Trial allowed; grandfathered for old pro users
            case FEATURE_USERS:// Grandfathered for old Pro users
                if($planDetails && $planDetails['trial']) {
                    // Do they have a non-trial plan?
                    $planDetails = $this->getPlanDetails(false, false);
                }

                return $selfHost || !empty($planDetails) && ($planDetails['plan'] == PLAN_ENTERPRISE || $planDetails['started'] <= date_create(PRO_USERS_GRANDFATHER_DEADLINE));

            // Enterprise; No Trial allowed
            case FEATURE_DOCUMENTS:
            case FEATURE_USER_PERMISSIONS:
                return $selfHost || !empty($planDetails) && $planDetails['plan'] == PLAN_ENTERPRISE && !$planDetails['trial'];

            default:
                return false;
        }
    }

    /**
     * @param null $plan_details
     * @return bool
     */
    public function isPro(&$plan_details = null)
    {
        if (!Utils::isNinjaProd()) {
            return true;
        }

        if ($this->isNinjaAccount()) {
            return true;
        }

        $plan_details = $this->getPlanDetails();

        return !empty($plan_details);
    }

    /**
     * @param null $plan_details
     * @return bool
     */
    public function isEnterprise(&$plan_details = null)
    {
        if (!Utils::isNinjaProd()) {
            return true;
        }

        if ($this->isNinjaAccount()) {
            return true;
        }

        $plan_details = $this->getPlanDetails();

        return $plan_details && $plan_details['plan'] == PLAN_ENTERPRISE;
    }

    /**
     * @param bool $include_inactive
     * @param bool $include_trial
     * @return array|null
     */
    public function getPlanDetails($include_inactive = false, $include_trial = true)
    {
        if (!$this->company) {
            return null;
        }

        $plan = $this->company->plan;
        $price = $this->company->plan_price;
        $trial_plan = $this->company->trial_plan;

        if((!$plan || $plan == PLAN_FREE) && (!$trial_plan || !$include_trial)) {
            return null;
        }

        $trial_active = false;
        if ($trial_plan && $include_trial) {
            $trial_started = DateTime::createFromFormat('Y-m-d', $this->company->trial_started);
            $trial_expires = clone $trial_started;
            $trial_expires->modify('+2 weeks');

            if ($trial_expires >= date_create()) {
               $trial_active = true;
            }
        }

        $plan_active = false;
        if ($plan) {
            if ($this->company->plan_expires == null) {
                $plan_active = true;
                $plan_expires = false;
            } else {
                $plan_expires = DateTime::createFromFormat('Y-m-d', $this->company->plan_expires);
                if ($plan_expires >= date_create()) {
                    $plan_active = true;
                }
            }
        }

        if (!$include_inactive && !$plan_active && !$trial_active) {
            return null;
        }

        // Should we show plan details or trial details?
        if (($plan && !$trial_plan) || !$include_trial) {
            $use_plan = true;
        } elseif (!$plan && $trial_plan) {
            $use_plan = false;
        } else {
            // There is both a plan and a trial
            if (!empty($plan_active) && empty($trial_active)) {
                $use_plan = true;
            } elseif (empty($plan_active) && !empty($trial_active)) {
                $use_plan = false;
            } elseif (!empty($plan_active) && !empty($trial_active)) {
                // Both are active; use whichever is a better plan
                if ($plan == PLAN_ENTERPRISE) {
                    $use_plan = true;
                } elseif ($trial_plan == PLAN_ENTERPRISE) {
                    $use_plan = false;
                } else {
                    // They're both the same; show the plan
                    $use_plan = true;
                }
            } else {
                // Neither are active; use whichever expired most recently
                $use_plan = $plan_expires >= $trial_expires;
            }
        }

        if ($use_plan) {
            return [
                'company_id' => $this->company->id,
                'num_users' => $this->company->num_users,
                'plan_price' => $price,
                'trial' => false,
                'plan' => $plan,
                'started' => DateTime::createFromFormat('Y-m-d', $this->company->plan_started),
                'expires' => $plan_expires,
                'paid' => DateTime::createFromFormat('Y-m-d', $this->company->plan_paid),
                'term' => $this->company->plan_term,
                'active' => $plan_active,
            ];
        } else {
            return [
                'company_id' => $this->company->id,
                'num_users' => 1,
                'plan_price' => 0,
                'trial' => true,
                'plan' => $trial_plan,
                'started' => $trial_started,
                'expires' => $trial_expires,
                'active' => $trial_active,
            ];
        }
    }

    /**
     * @return bool
     */
    public function isTrial()
    {
        if (!Utils::isNinjaProd()) {
            return false;
        }

        $plan_details = $this->getPlanDetails();

        return $plan_details && $plan_details['trial'];
    }

    /**
     * @param null $plan
     * @return array|bool
     */
    public function isEligibleForTrial($plan = null)
    {
        if (!$this->company->trial_plan) {
            if ($plan) {
                return $plan == PLAN_PRO || $plan == PLAN_ENTERPRISE;
            } else {
                return [PLAN_PRO, PLAN_ENTERPRISE];
            }
        }

        if ($this->company->trial_plan == PLAN_PRO) {
            if ($plan) {
                return $plan != PLAN_PRO;
            } else {
                return [PLAN_ENTERPRISE];
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getCountTrialDaysLeft()
    {
        $planDetails = $this->getPlanDetails(true);

        if(!$planDetails || !$planDetails['trial']) {
            return 0;
        }

        $today = new DateTime('now');
        $interval = $today->diff($planDetails['expires']);

        return $interval ? $interval->d : 0;
    }

    /**
     * @return mixed
     */
    public function getRenewalDate()
    {
        $planDetails = $this->getPlanDetails();

        if ($planDetails) {
            $date = $planDetails['expires'];
            $date = max($date, date_create());
        } else {
            $date = date_create();
        }

        return $date->format('Y-m-d');
    }

    /**
     * @return float|null
     */
    public function getLogoSize()
    {
        if(!$this->hasLogo()){
            return null;
        }

        return round($this->logo_size / 1000);
    }

    /**
     * @return bool
     */
    public function isLogoTooLarge()
    {
        return $this->getLogoSize() > MAX_LOGO_FILE_SIZE;
    }

    /**
     * @param $eventId
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getSubscription($eventId)
    {
        return Subscription::where('account_id', '=', $this->id)->where('event_id', '=', $eventId)->first();
    }

    /**
     * @return $this
     */
    public function hideFieldsForViz()
    {
        foreach ($this->clients as $client) {
            $client->setVisible([
                'public_id',
                'name',
                'balance',
                'paid_to_date',
                'invoices',
                'contacts',
            ]);

            foreach ($client->invoices as $invoice) {
                $invoice->setVisible([
                    'public_id',
                    'invoice_number',
                    'amount',
                    'balance',
                    'invoice_status_id',
                    'invoice_items',
                    'created_at',
                    'is_recurring',
                    'invoice_type_id',
                ]);

                foreach ($invoice->invoice_items as $invoiceItem) {
                    $invoiceItem->setVisible([
                        'product_key',
                        'cost',
                        'qty',
                    ]);
                }
            }

            foreach ($client->contacts as $contact) {
                $contact->setVisible([
                    'public_id',
                    'first_name',
                    'last_name',
                    'email', ]);
            }
        }

        return $this;
    }

    /**
     * @param $entityType
     * @return mixed
     */
    public function getDefaultEmailSubject($entityType)
    {
        if (strpos($entityType, 'reminder') !== false) {
            $entityType = 'reminder';
        }

        return trans("texts.{$entityType}_subject", ['invoice' => '$invoice', 'account' => '$account']);
    }

    /**
     * @param $entityType
     * @return mixed
     */
    public function getEmailSubject($entityType)
    {
        if ($this->hasFeature(FEATURE_CUSTOM_EMAILS)) {
            $field = "email_subject_{$entityType}";
            $value = $this->$field;

            if ($value) {
                return $value;
            }
        }

        return $this->getDefaultEmailSubject($entityType);
    }

    /**
     * @param $entityType
     * @param bool $message
     * @return string
     */
    public function getDefaultEmailTemplate($entityType, $message = false)
    {
        if (strpos($entityType, 'reminder') !== false) {
            $entityType = ENTITY_INVOICE;
        }

        $template = '<div>$client,</div><br>';

        if ($this->hasFeature(FEATURE_CUSTOM_EMAILS) && $this->email_design_id != EMAIL_DESIGN_PLAIN) {
            $template .= '<div>' . trans("texts.{$entityType}_message_button", ['amount' => '$amount']) . '</div><br>' .
                         '<div style="text-align: center;">$viewButton</div><br>';
        } else {
            $template .= '<div>' . trans("texts.{$entityType}_message", ['amount' => '$amount']) . '</div><br>' .
                         '<div>$viewLink</div><br>';
        }

        if ($message) {
            $template .= "$message<p/>\r\n\r\n";
        }

        return $template . '$footer';
    }

    /**
     * @param $entityType
     * @param bool $message
     * @return mixed
     */
    public function getEmailTemplate($entityType, $message = false)
    {
        $template = false;

        if ($this->hasFeature(FEATURE_CUSTOM_EMAILS)) {
            $field = "email_template_{$entityType}";
            $template = $this->$field;
        }

        if (!$template) {
            $template = $this->getDefaultEmailTemplate($entityType, $message);
        }

        // <br/> is causing page breaks with the email designs
        return str_replace('/>', ' />', $template);
    }

    /**
     * @param string $view
     * @return string
     */
    public function getTemplateView($view = '')
    {
        return $this->getEmailDesignId() == EMAIL_DESIGN_PLAIN ? $view : 'design' . $this->getEmailDesignId();
    }

    /**
     * @return mixed|string
     */
    public function getEmailFooter()
    {
        if ($this->email_footer) {
            // Add line breaks if HTML isn't already being used
            return strip_tags($this->email_footer) == $this->email_footer ? nl2br($this->email_footer) : $this->email_footer;
        } else {
            return '<p><div>' . trans('texts.email_signature') . "\n<br>\$account</div></p>";
        }
    }

    /**
     * @param $reminder
     * @return bool
     */
    public function getReminderDate($reminder)
    {
        if ( ! $this->{"enable_reminder{$reminder}"}) {
            return false;
        }

        $numDays = $this->{"num_days_reminder{$reminder}"};
        $plusMinus = $this->{"direction_reminder{$reminder}"} == REMINDER_DIRECTION_AFTER ? '-' : '+';

        return date('Y-m-d', strtotime("$plusMinus $numDays days"));
    }

    /**
     * @param Invoice $invoice
     * @return bool|string
     */
    public function getInvoiceReminder(Invoice $invoice)
    {
        for ($i=1; $i<=3; $i++) {
            if ($date = $this->getReminderDate($i)) {
                $field = $this->{"field_reminder{$i}"} == REMINDER_FIELD_DUE_DATE ? 'due_date' : 'invoice_date';
                if ($invoice->$field == $date) {
                    return "reminder{$i}";
                }
            }
        }

        return false;
    }

    /**
     * @param null $storage_gateway
     * @return bool
     */
    public function showTokenCheckbox(&$storage_gateway = null)
    {
        if (!($storage_gateway = $this->getTokenGatewayId())) {
            return false;
        }

        return $this->token_billing_type_id == TOKEN_BILLING_OPT_IN
                || $this->token_billing_type_id == TOKEN_BILLING_OPT_OUT;
    }

    /**
     * @return bool
     */
    public function getTokenGatewayId() {
        if ($this->isGatewayConfigured(GATEWAY_STRIPE)) {
            return GATEWAY_STRIPE;
        } elseif ($this->isGatewayConfigured(GATEWAY_BRAINTREE)) {
            return GATEWAY_BRAINTREE;
        } elseif ($this->isGatewayConfigured(GATEWAY_WEPAY)) {
            return GATEWAY_WEPAY;
        } else {
            return false;
        }
    }

    /**
     * @return bool|void
     */
    public function getTokenGateway() {
        $gatewayId = $this->getTokenGatewayId();
        if (!$gatewayId) {
            return;
        }

        return $this->getGatewayConfig($gatewayId);
    }

    /**
     * @return bool
     */
    public function selectTokenCheckbox()
    {
        return $this->token_billing_type_id == TOKEN_BILLING_OPT_OUT;
    }

    /**
     * @return string
     */
    public function getSiteUrl()
    {
        $url = SITE_URL;
        $iframe_url = $this->iframe_url;

        if ($iframe_url) {
            return "{$iframe_url}/?";
        } else if ($this->subdomain) {
            $url = Utils::replaceSubdomain($url, $this->subdomain);
        }

        return $url;
    }

    /**
     * @param $host
     * @return bool
     */
    public function checkSubdomain($host)
    {
        if (!$this->subdomain) {
            return true;
        }

        $server = explode('.', $host);
        $subdomain = $server[0];

        if (!in_array($subdomain, ['app', 'www']) && $subdomain != $this->subdomain) {
            return false;
        }

        return true;
    }

    /**
     * @param $field
     * @param bool $entity
     * @return bool
     */
    public function showCustomField($field, $entity = false)
    {
        if ($this->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            return $this->$field ? true : false;
        }

        if (!$entity) {
            return false;
        }

        // convert (for example) 'custom_invoice_label1' to 'invoice.custom_value1'
        $field = str_replace(['invoice_', 'label'], ['', 'value'], $field);

        return Utils::isEmpty($entity->$field) ? false : true;
    }

    /**
     * @return bool
     */
    public function attachPDF()
    {
        return $this->hasFeature(FEATURE_PDF_ATTACHMENT) && $this->pdf_email_attachment;
    }

    /**
     * @return mixed
     */
    public function getEmailDesignId()
    {
        return $this->hasFeature(FEATURE_CUSTOM_EMAILS) ? $this->email_design_id : EMAIL_DESIGN_PLAIN;
    }

    /**
     * @return string
     */
    public function clientViewCSS(){
        $css = '';

        if ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $bodyFont = $this->getBodyFontCss();
            $headerFont = $this->getHeaderFontCss();

            $css = 'body{'.$bodyFont.'}';
            if ($headerFont != $bodyFont) {
                $css .= 'h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6{'.$headerFont.'}';
            }
        }
        if ($this->hasFeature(FEATURE_CLIENT_PORTAL_CSS)) {
            // For self-hosted users, a white-label license is required for custom CSS
            $css .= $this->client_view_css;
        }

        return $css;
    }

    /**
     * @param string $protocol
     * @return string
     */
    public function getFontsUrl($protocol = ''){
	    return '';
        $bodyFont = $this->getHeaderFontId();
        $headerFont = $this->getBodyFontId();

        $google_fonts = [$bodyFontSettings['google_font']];

        if($headerFont != $bodyFont){
            $google_fonts[] = $headerFontSettings['google_font'];
        }

        return ($protocol?$protocol.':':'').'//fonts.googleapis.com/css?family='.implode('|',$google_fonts);
    }

    /**
     * @return mixed
     */
    public function getHeaderFontId() {
        return ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) && $this->header_font_id) ? $this->header_font_id : DEFAULT_HEADER_FONT;
    }

    /**
     * @return mixed
     */
    public function getBodyFontId() {
        return ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) && $this->body_font_id) ? $this->body_font_id : DEFAULT_BODY_FONT;
    }

    /**
     * @return null
     */
    public function getHeaderFontName(){
        return Utils::getFromCache($this->getHeaderFontId(), 'fonts')['name'];
    }

    /**
     * @return null
     */
    public function getBodyFontName(){
        return Utils::getFromCache($this->getBodyFontId(), 'fonts')['name'];
    }

    /**
     * @param bool $include_weight
     * @return string
     */
    public function getHeaderFontCss($include_weight = true){
        $font_data = Utils::getFromCache($this->getHeaderFontId(), 'fonts');
        $css = 'font-family:'. isset($font_data['css_stack']) ? $font_data['css_stack'] : '' .';';

        if($include_weight){
            $css .= 'font-weight:'. isset($font_data['css_weight']) ? $font_data['css_weight'] : '' .';';
        }

        return $css;
    }

    /**
     * @param bool $include_weight
     * @return string
     */
    public function getBodyFontCss($include_weight = true){
        $font_data = Utils::getFromCache($this->getBodyFontId(), 'fonts');
        $css = 'font-family:'. isset($font_data['css_stack']) ? $font_data['css_stack'] : '' .';';

        if($include_weight){
            $css .= 'font-weight:'. isset($font_data['css_weight']) ? $font_data['css_weight'] : '' .';';
        }

        return $css;
    }

    /**
     * @return array
     */
    public function getFonts(){
        return array_unique([$this->getHeaderFontId(), $this->getBodyFontId()]);
    }

    /**
     * @return array
     */
    public function getFontsData(){
        $data = [];

        foreach($this->getFonts() as $font){
            $data[] = Utils::getFromCache($font, 'fonts');
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getFontFolders(){
        return array_map(function($item){return $item['folder'];}, $this->getFontsData());
    }

    public function isModuleEnabled($entityType)
    {
        if ( ! in_array($entityType, [
            ENTITY_RECURRING_INVOICE,
            ENTITY_CREDIT,
            ENTITY_QUOTE,
            ENTITY_TASK,
            ENTITY_EXPENSE,
            ENTITY_VENDOR,
        ])) {
            return true;
        }

        return $this->enabled_modules & static::$modules[$entityType];
    }

    public function showAuthenticatePanel($invoice)
    {
        return $this->showAcceptTerms($invoice) || $this->showSignature($invoice);
    }

    public function showAcceptTerms($invoice)
    {
        if ( ! $this->isPro() || ! $invoice->terms) {
            return false;
        }

        return $invoice->is_quote ? $this->show_accept_quote_terms : $this->show_accept_invoice_terms;
    }

    public function showSignature($invoice)
    {
        if ( ! $this->isPro()) {
            return false;
        }

        return $invoice->is_quote ? $this->require_quote_signature : $this->require_invoice_signature;
    }

    public  function amountToLetters($num, $currency) {
          $fem = false;
          $dec = true;
          $matuni[2]  = "dos";
          $matuni[3]  = "tres";
          $matuni[4]  = "cuatro";
          $matuni[5]  = "cinco";
          $matuni[6]  = "seis";
          $matuni[7]  = "siete";
          $matuni[8]  = "ocho";
          $matuni[9]  = "nueve";
          $matuni[10] = "diez";
          $matuni[11] = "once";
          $matuni[12] = "doce";
          $matuni[13] = "trece";
          $matuni[14] = "catorce";
          $matuni[15] = "quince";
          $matuni[16] = "dieciseis";
          $matuni[17] = "diecisiete";
          $matuni[18] = "dieciocho";
          $matuni[19] = "diecinueve";
          $matuni[20] = "veinte";
          $matunisub[2] = "dos";
          $matunisub[3] = "tres";
          $matunisub[4] = "cuatro";
          $matunisub[5] = "quin";
          $matunisub[6] = "seis";
          $matunisub[7] = "sete";
          $matunisub[8] = "ocho";
          $matunisub[9] = "nove";
          $matdec[2] = "veint";
          $matdec[3] = "treinta";
          $matdec[4] = "cuarenta";
          $matdec[5] = "cincuenta";
          $matdec[6] = "sesenta";
          $matdec[7] = "setenta";
          $matdec[8] = "ochenta";
          $matdec[9] = "noventa";
          $matsub[3]  = 'mill';
          $matsub[5]  = 'bill';
          $matsub[7]  = 'mill';
          $matsub[9]  = 'trill';
          $matsub[11] = 'mill';
          $matsub[13] = 'bill';
          $matsub[15] = 'mill';
          $matmil[4]  = 'millones';
          $matmil[6]  = 'billones';
          $matmil[7]  = 'de billones';
          $matmil[8]  = 'millones de billones';
          $matmil[10] = 'trillones';
          $matmil[11] = 'de trillones';
          $matmil[12] = 'millones de trillones';
          $matmil[13] = 'de trillones';
          $matmil[14] = 'billones de trillones';
          $matmil[15] = 'de billones de trillones';
          $matmil[16] = 'millones de billones de trillones';

          //Zi hack
          $float= explode('.', $num);

          $num=$float[0];
          if($num == 100){
            return 'Cien Lempiras con 00/100';
          }
          $num = trim((string)@$num);
          if ($num[0] == '-') {
             $neg = 'menos ';
             $num = substr($num, 1);
          }else
             $neg = '';
          while (is_array($num) && $num[0] == '0') $num = substr($num, 1);
          if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
          $zeros = true;
          $punt = false;
          $ent = '';
          $fra = '';
          for ($c = 0; $c < strlen($num); $c++) {
             $n = $num[$c];

             if (! (strpos(".,'''", $n) === false)) {
                if ($punt) break;
                else{
                   $punt = true;
                   continue;
                }
             }elseif (! (strpos('0123456789', $n) === false)) {
                if ($punt) {
                   if ($n != '0') $zeros = false;
                   $fra .= $n;
                }else
                   $ent .= $n;
             }else
                break;
          }
          $ent = '     ' . $ent;
          if ($dec and $fra and ! $zeros) {
             $fin = ' coma';
             for ($n = 0; $n < strlen($fra); $n++) {
                if (($s = $fra[$n]) == '0')
                   $fin .= ' cero';
                elseif ($s == '1')
                   $fin .= $fem ? ' una' : ' un';
                else
                   $fin .= ' ' . $matuni[$s];
             }
          }else
             $fin = '';
          if ((int)$ent === 0) return 'Cero ' . $fin;
          $tex = '';
          $sub = 0;
          $mils = 0;
          $neutro = false;
          while ( ($num = substr($ent, -3)) != '   ') {
             $ent = substr($ent, 0, -3);
             if (++$sub < 3 and $fem) {
                $matuni[1] = 'una';
                $subcent = 'as';
             }else{
                $matuni[1] = $neutro ? 'un' : 'uno';
                $subcent = 'os';
             }
             $t = '';
             $n2 = substr($num, 1);
             if ($n2 == '00') {
             }elseif ($n2 < 21)
                $t = ' ' . $matuni[(int)$n2];
             elseif ($n2 < 30) {
                $n3 = $num[2];
                if ($n3 != 0) $t = 'i' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
             }else{
                $n3 = $num[2];
                if ($n3 != 0) $t = ' y ' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
             }
             $n = $num[0];
             if ($n == 1) {
                $t = ' ciento' . $t;
             }elseif ($n == 5){
                $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
             }elseif ($n != 0){
                $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
             }
             if ($sub == 1) {
             }elseif (! isset($matsub[$sub])) {
                if ($num == 1) {
                   $t = ' mil';
                }elseif ($num > 1){
                   $t .= ' mil';
                }
             }elseif ($num == 1) {
                $t .= ' ' . $matsub[$sub] . '?n';
             }elseif ($num > 1){
                $t .= ' ' . $matsub[$sub] . 'ones';
             }
             if ($num == '000') $mils ++;
             elseif ($mils != 0) {
                if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
                $mils = 0;
             }
             $neutro = true;
             $tex = $t . $tex;
          }

          $tex = $neg . substr($tex, 1) . $fin;
          $ff = isset($float[1]) ? $float[1] : '00';
          $end_num=ucfirst($tex).' con '.$ff.'/100 '. $currency;
          return $end_num;
    }

       public function addProductToRequest($product){
             $data = [];
                if(!$product){
                        return ['error' => true, 'msg' => 'No se pudo agregar el producto'];
                }
                $data['product_key'] = $product->product_key;
                $data['qty'] = 1;
                $data['products'] = [$data];
                $data['status'] = 1;
                $_response = $this->saveData($data);
                return $_response;
     }

     public function saveData($data){
                $items_count = 0;
                $grand_total = 0;
                $_request = null;
                if(isset($data['request_id'])){
                        $_request = OrderRequest::find($data['request_id']);
                }else{
                        $_request = OrderRequest::where('account_id', Auth::user()->account_id)->where('status', 1)->first();
                }
                $error = false;
                $orderRequest = isset($_request) ? $_request : new OrderRequest();
               $orderRequest->account_id = isset($orderRequest->account_id) ? $orderRequest->account_id : Auth::user()->account_id;
                $orderRequest->status =  $data['status'];
                if(isset($data['client_id'])){
                         $orderRequest->client_id = $data['client_id'];
                }
               $orderRequest->warehouse_account_id = 17;
               $orderRequest->items_count = isset($data['products']) ? count($data['products']) : 0;
               $orderRequest->grand_total = 0;
               $orderRequest->save();
               if(isset($data['products'])){
                  foreach($data['products'] as $product){
                       $product_key = trim($product['product_key']);
                       $qty = trim($product['qty']);
                       $_product = Product::where('account_id', 17)->where('product_key', $product_key)->first();
                       if($_product){
                        $_productRequest = ProductRequest::where('request_id', $orderRequest->id)->where('product_key', $product_key)->where('is_enabled', 1)->first();
                        $productRequest = ($_productRequest) ? ($_productRequest) : new ProductRequest();

                       $productRequest->request_id = $orderRequest->id;
                       $productRequest->product_id = $_product->id;
                       $productRequest->product_key = $_product->product_key;
                       $productRequest->description = $_product->notes;
                       $productRequest->account_id = Auth::user()->account_id;
                       $productRequest->warehouse_account_id = $_product->account_id;
                       $productRequest->user_id = Auth::user()->id;
                       $productRequest->qty = ($_productRequest) ? ($_productRequest->qty += $qty) : $qty;
                       $productRequest->price = $_product->price;
                       $productRequest->created_at = date('Y-m-d H:i:s');
                       $productRequest->save();

                       }else{
                          $error = true;
                       }
                  }

               }
               if($error){ return ['error' => true, 'msg' => 'Producto no existe en bodega']; }
               foreach($orderRequest->products as $product){
                        $items_count += $product->qty;
                        $grand_total += $product->price;
               }
               $orderRequest->grand_total = $grand_total;
               $orderRequest->items_count = $items_count;
               $orderRequest->save();
               return $orderRequest;
     }

     public function financeAccounts(){
	return FinanceAccount::scope()->get();
     }

     public function getVendors(){
        $vendors = Vendor::select('id', 'name')->whereNull('deleted_at')->orderBy('name', 'asc')->get();
        return $vendors;
    }

    public function stock_in_stores($account_id) {
        
        $products = Product::where('account_id', $account_id)
            ->where('qty', '>', 0)
            ->selectRaw('SUM(wholesale_price * qty) as total_price, SUM(qty) as total_qty')
            ->first();

        return  $products;
    }
}

Account::updated(function ($account)
{
    // prevent firing event if the invoice/quote counter was changed
    // TODO: remove once counters are moved to separate table
    $dirty = $account->getDirty();
    if (isset($dirty['invoice_number_counter']) || isset($dirty['quote_number_counter'])) {
        return;
    }

    Event::fire(new UserSettingsChanged());
});
