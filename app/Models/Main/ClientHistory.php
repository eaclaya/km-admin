<?php namespace App\Models\Main;

use Utils;
use DB;
use Carbon;
use Auth;
/**
 * Class Client
 */
class ClientHistory extends ModelDBMain
{

    protected $connection = 'main';

    /**
     * @var string
     */
    protected $table = 'clients_history';
    /**
     * @var array
     */

    /**
     * @var array
     */
    protected $fillable = [
	    "user_id",
        "account_id",
        "seller_id",
        "currency_id",
        "created_at",
        "updated_at",
        "name",
        "address1",
        "address2",
        "city",
        "state",
        "postal_code",
        "country_id",
        "work_phone",
        "private_notes",
        "balance",
        "paid_to_date",
        "last_login",
        "website",
        "industry_id",
        "size_id",
        "is_deleted",
        "payment_terms",
        "public_id",
        "custom_value1",
        "custom_value2",
        "vat_number",
        "id_number",
        "language_id",
        "type",
        "extra_tax",
        "longitude",
        "latitude",
        "address",
        "price_group",
        "is_company",
        "points",
        'route_id',
        'frequency_id',
        'frequency_day'
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
        return $this->hasMany('App\Models\Main\Invoice');
    }


    public function visits(){
	return $this->hasMany('App\Models\Main\Visit')->orderBy('id', 'DESC');
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

}

