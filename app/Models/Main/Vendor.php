<?php namespace App\Models\Main;

use Utils;
use DB;
use App\Events\VendorWasCreated;
use App\Events\VendorWasUpdated;
use App\Events\VendorWasDeleted;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Vendor
 */
class Vendor extends ModelDBMain
{

    protected $connection = 'main';

    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter    = 'App\Ninja\Presenters\VendorPresenter';
    /**
     * @var array
     */
    protected $dates        = ['deleted_at'];
    /**
     * @var array
     */
    protected $fillable     = [
        'id',
        'name',
        'id_number',
        'credit_days',
        'vat_number',
        'work_phone',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'currency_id',
        'website',
        'transaction_name',
        'bank_id',
        'bank_account'
    ];

    /**
     * @var string
     */
    public static $fieldName        = 'name';
    /**
     * @var string
     */
    public static $fieldPhone       = 'work_phone';
    /**
     * @var string
     */
    public static $fieldAddress1    = 'address1';
    /**
     * @var string
     */
    public static $fieldAddress2    = 'address2';
    /**
     * @var string
     */
    public static $fieldCity        = 'city';
    /**
     * @var string
     */
    public static $fieldState       = 'state';
    /**
     * @var string
     */
    public static $fieldPostalCode  = 'postal_code';
    /**
     * @var string
     */
    public static $fieldNotes       = 'notes';
    /**
     * @var string
     */
    public static $fieldCountry     = 'country';

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            Vendor::$fieldName,
            Vendor::$fieldPhone,
            Vendor::$fieldAddress1,
            Vendor::$fieldAddress2,
            Vendor::$fieldCity,
            Vendor::$fieldState,
            Vendor::$fieldPostalCode,
            Vendor::$fieldCountry,
            Vendor::$fieldNotes,
            VendorContact::$fieldFirstName,
            VendorContact::$fieldLastName,
            VendorContact::$fieldPhone,
            VendorContact::$fieldEmail,
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
        ];
    }

    public function purchases(){
	return $this->hasMany('App\Models\Main\Purchase')->orderBy('id', 'desc');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    
    public function bank()
    {
        return $this->belongsTo('App\Models\Main\Bank');
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
    public function payments()
    {
        return $this->hasMany('App\Models\Main\Payment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendor_contacts()
    {
        return $this->hasMany('App\Models\Main\VendorContact');
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
    public function expenses()
    {
        return $this->hasMany('App\Models\Main\Expense','vendor_id','id');
    }

    /**
     * @param $data
     * @param bool $isPrimary
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addVendorContact($data, $isPrimary = false)
    {
        //$publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $publicId = isset($data['public_id']) ? $data['public_id'] : (isset($data['id']) ? $data['id'] : false);

        if ($publicId && $publicId != '-1') {
            $contact = VendorContact::scope($publicId)->firstOrFail();
        } else {
            $contact = VendorContact::createNew();
        }

        $contact->fill($data);
        $contact->is_primary = $isPrimary;

        return $this->vendor_contacts()->save($contact);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/vendors/{$this->public_id}";
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
    public function getDisplayName()
    {
        return $this->getName();
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
     * @return string
     */
    public function getEntityType()
    {
        return 'vendor';
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
     * @return float|int
     */
    public function getTotalExpense()
    {
       $expenses = DB::table('expenses')
                ->where('vendor_id', '=', $this->id)
                ->whereNull('deleted_at')
		->sum('balance');
       return $expenses;
       $total = 0;
       foreach($expenses as $expense){
		$total += ($expense->balance);
       }
    }

    public function lastExpenseExpirationDate(){
	$expenses = Expense::where('vendor_id', $this->id)->where('balance', '>', 0)->orderBy('expiration_date', 'ASC')->get();
	 $date = 'N/A';
        foreach($expenses as $expense){
                if($expense->expiration_date < date('Y-m-d')){
                        $date = $expense->expiration_date;
                        break;
                }
	}
	return $date;

    }

    public function nextExpenseExpirationDate(){
	$expenses = Expense::where('vendor_id', $this->id)->where('balance', '>', 0)->orderBy('expiration_date', 'ASC')->get();
	$date = 'N/A';
	foreach($expenses as $expense){
		if($expense->expiration_date > date('Y-m-d')){
			$date = $expense->expiration_date;
			break;
		}
	}
	return $date;
    }
}

Vendor::creating(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::created(function ($vendor) {
    event(new VendorWasCreated($vendor));
});

Vendor::updating(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::updated(function ($vendor) {
    event(new VendorWasUpdated($vendor));
});


Vendor::deleting(function ($vendor) {
    $vendor->setNullValues();
});

Vendor::deleted(function ($vendor) {
    event(new VendorWasDeleted($vendor));
});
