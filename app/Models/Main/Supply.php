<?php namespace App\Models\Main;

use Utils;
use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Supply
 */
class Supply extends EntityModel implements BalanceAffecting
{
    use PresentableTrait;
    use OwnedByClientTrait;
    use SoftDeletes {
        SoftDeletes::trashed as parentTrashed;
    }

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\SupplyPresenter';
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_recurring' => 'boolean',
        'has_tasks' => 'boolean',
        'client_enable_auto_bill' => 'boolean',
        'has_expenses' => 'boolean',
    ];

    // used for custom supply numbers
    /**
     * @var array
     */
    public static $patternFields = [
        'counter',
        'custom1',
        'custom2',
        'userId',
        'year',
        'date:',
    ];

    /**
     * @var string
     */
    public static $fieldSupplyNumber = 'supply_number';
    /**
     * @var string
     */
    public static $fieldSupplyDate = 'supply_date';
    /**
     * @var string
     */
    public static $fieldDueDate = 'due_date';
    /**
     * @var string
     */
    public static $fieldAmount = 'amount';
    /**
     * @var string
     */
    public static $fieldPaid = 'paid';
    /**
     * @var string
     */
    public static $fieldNotes = 'notes';
    /**
     * @var string
     */
    public static $fieldTerms = 'terms';

    /**
     * @return array
     */
    public static function getImportColumns()
    {
        return [
            Client::$fieldName,
            Supply::$fieldSupplyNumber,
            Supply::$fieldSupplyDate,
            Supply::$fieldDueDate,
            Supply::$fieldAmount,
            Supply::$fieldPaid,
            Supply::$fieldNotes,
            Supply::$fieldTerms,
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'number^po' => 'supply_number',
            'amount' => 'amount',
            'organization' => 'name',
            'paid^date' => 'paid',
            'supply_date|create_date' => 'supply_date',
            'terms' => 'terms',
            'notes' => 'notes',
        ];
    }

    /**
     * @return string
     */
    public function getRoute()
    {

        return "/supplies/{$this->public_id}/edit";
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->is_recurring ? trans('texts.recurring') : $this->supply_number;
    }

    /**
     * @return bool
     */
    public function affectsBalance()
    {
        return $this->isType(SUPPLY_TYPE_STANDARD) && !$this->is_recurring;
    }

    /**
     * @return float|int
     */
    public function getAdjustment()
    {
        if (!$this->affectsBalance()) {
            return 0;
        }

        return $this->getRawAdjustment();
    }

    /**
     * @return float
     */
    private function getRawAdjustment()
    {
        return floatval($this->amount) - floatval($this->getOriginal('amount'));
    }

    /**
     * @return bool
     */
    public function isChanged()
    {
        if ($this->getRawAdjustment() != 0) {
            return true;
        }

        foreach ([
            'supply_number',
            'po_number',
            'supply_date',
            'due_date',
            'terms',
            'public_notes',
            'supply_footer',
            'partial',
        ] as $field) {
            if ($this->$field != $this->getOriginal($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $calculate
     * @return int|mixed
     */
    public function getAmountPaid($calculate = false)
    {

            return ($this->amount - $this->balance);
    }

    /**
     * @return bool
     */
    public function trashed()
    {
        if ($this->client && $this->client->trashed()) {
            return true;
        }

        return self::parentTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }

    public function billing()
    {
        return $this->belongsTo('App\Models\Main\Billing');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    public function employee(){
	return $this->belongsTo('App\Models\Main\Employee');
    }
    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }
	
    public function request(){
	return $this->belongsTo('App\Models\Main\OrderRequest');
    }
    /**
     * @return mixed
     */
    public function supply_items()
    {
        return $this->hasMany('App\Models\Main\SupplyItem')->orderBy('notes', 'ASC');
    }

    public function comments(){
	return $this->hasMany('App\Models\Main\SupplyComment');
    }
	
    /**
     * @return mixed
     */
    public function documents()
    {
        return $this->hasMany('App\Models\Main\Document')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supply_status()
    {
        return $this->belongsTo('App\Models\Main\SupplyStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supply_design()
    {
        return $this->belongsTo('App\Models\Main\SupplyDesign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recurring_supply()
    {
        return $this->belongsTo('App\Models\Main\Supply');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recurring_supplies()
    {
        return $this->hasMany('App\Models\Main\Supply', 'recurring_supply_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function frequency()
    {
        return $this->belongsTo('App\Models\Main\Frequency');
    }

    /**
     * @return mixed
     */
    public function invitations()
    {
        return $this->hasMany('App\Models\Main\Invitation')->orderBy('invitations.contact_id');
    }

    /**
     * @return mixed
     */

    /**
     * @param $query
     * @return mixed
     */
    public function scopeSupplies($query)
    {
        return $query->where('supply_type_id', '=', SUPPLY_TYPE_STANDARD)
                     ->where('is_recurring', '=', false);
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\Main\User', 'seller_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeQuotes($query)
    {
        return $query->where('is_recurring', '=', false);
    }

    /**
     * @param $query
     * @param $typeId
     * @return mixed
     */
    public function scopeSupplyType($query, $typeId)
    {
        return $query->where('supply_type_id', '=', $typeId);
    }

    /**
     * @param $typeId
     * @return bool
     */
    public function isType($typeId) {
        return $this->supply_type_id == $typeId;
    }

    /**
     * @return bool
     */
    public function isQuote() {
        return false;
    }

    /**
     * @return bool
     */
    public function isSupply() {
        return $this->isType(SUPPLY_TYPE_STANDARD) && ! $this->is_recurring;
    }

    /**
     * @param bool $notify
     */
    public function markInvitationsSent($notify = false)
    {
        foreach ($this->invitations as $invitation) {
            $this->markInvitationSent($invitation, false, $notify);
        }
    }

    /**
     * @param $invitation
     * @param bool $messageId
     * @param bool $notify
     */
    public function markInvitationSent($invitation, $messageId = false, $notify = true)
    {

        if (!$this->isSent()) {
            $this->supply_status_id = SUPPLY_STATUS_SENT;
            $this->save();

        }

        $invitation->markSent($messageId);

        // if the user marks it as sent rather than acually sending it
        // then we won't track it in the activity log
        if (!$notify) {
            return;
        }

    }

    public function markViewed()
    {
        if (!$this->isViewed()) {
            $this->supply_status_id = SUPPLY_STATUS_VIEWED;
            $this->save();
        }
    }

    /**
     * @param bool $save
     */
    public function updatePaidStatus($save = true)
    {
        $statusId = false;
        if ($this->amount > 0 && $this->balance == 0) {
            $statusId = SUPPLY_STATUS_PAID;
        } elseif ($this->balance > 0 && $this->balance < $this->amount) {
            $statusId = SUPPLY_STATUS_PARTIAL;
        } elseif ($this->isPartial() && $this->balance > 0) {
            $statusId = ($this->balance == $this->amount ? SUPPLY_STATUS_SENT : SUPPLY_STATUS_PARTIAL);
        }

        if ($statusId && $statusId != $this->supply_status_id) {
            $this->supply_status_id = $statusId;
            if ($save) {
                $this->save();
            }
        }
    }

    public function markApproved()
    {
    }

    /**
     * @param $balanceAdjustment
     * @param int $partial
     */
    public function updateBalances($balanceAdjustment, $partial = 0)
    {
        if ($this->is_deleted) {
            return;
        }

        $this->balance = $this->balance + $balanceAdjustment;

        if ($this->partial > 0) {
            $this->partial = $partial;
        }

        $this->save();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->is_recurring ? trans('texts.recurring') : $this->supply_number;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        $entityType = $this->getEntityType();
        return trans("texts.$entityType") . '_' . $this->supply_number . '.pdf';
    }

    /**
     * @return string
     */
    public function getPDFPath()
    {
        return storage_path() . '/pdfcache/cache-' . $this->id . '.pdf';
    }

    public function canBePaid()
    {
        return floatval($this->balance) > 0 && ! $this->is_deleted;
    }

    /**
     * @param $supply
     * @return string
     */
    public static function calcLink($supply)
    {
        return link_to('supplies/' . $supply->public_id, $supply->supply_number);
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return self::calcLink($this);
    }

    public function getInvitationLink($type = 'view', $forceOnsite = false)
    {
        if ( ! $this->relationLoaded('invitations')) {
            $this->load('invitations');
        }

        return $this->invitations[0]->getLink($type, $forceOnsite);
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_SUPPLY;
    }

    public function subEntityType()
    {
        if ($this->is_recurring) {
            return ENTITY_RECURRING_SUPPLY;
        } else {
            return $this->getEntityType();
        }
    }

    /**
     * @return bool
     */
    public function isSent()
    {
        return $this->supply_status_id >= SUPPLY_STATUS_SENT;
    }

    /**
     * @return bool
     */
    public function isViewed()
    {
        return $this->supply_status_id >= SUPPLY_STATUS_VIEWED;
    }

    /**
     * @return bool
     */
    public function isPartial()
    {
        return $this->supply_status_id >= SUPPLY_STATUS_PARTIAL;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->supply_status_id >= SUPPLY_STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isOverdue()
    {
        if ( ! $this->due_date) {
            return false;
        }

        return time() > strtotime($this->due_date);
    }

    /**
     * @return mixed
     */
    public function getRequestedAmount()
    {
        return $this->partial > 0 ? $this->partial : $this->balance;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        if ($this->client->currency) {
            return $this->client->currency->code;
        } elseif ($this->account->currency) {
            return $this->account->currency->code;
        } else {
            return 'USD';
        }
    }

    /**
     * @return $this
     */
    public function hidePrivateFields()
    {
        $this->setVisible([
            'supply_number',
            'discount',
            'is_amount_discount',
            'po_number',
            'supply_date',
            'due_date',
            'terms',
            'supply_footer',
            'public_notes',
            'amount',
            'balance',
            'supply_items',
            'documents',
            'expenses',
            'client',
            'tax_name1',
            'tax_rate1',
            'tax_name2',
            'tax_rate2',
            'account',
            'supply_design',
            'supply_design_id',
            'supply_fonts',
            'features',
            'supply_type_id',
            'custom_value1',
            'custom_value2',
            'custom_taxes1',
            'custom_taxes2',
            'partial',
            'has_tasks',
            'custom_text_value1',
            'custom_text_value2',
            'has_expenses',
        ]);
	/*
        $this->client->setVisible([
            'name',
            'id_number',
            'vat_number',
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'work_phone',
            'payment_terms',
            'contacts',
            'country',
            'currency_id',
            'country_id',
            'custom_value1',
            'custom_value2',
        ]);
	*/
        $this->account->setVisible([
            'name',
            'website',
            'id_number',
            'vat_number',
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'work_phone',
            'work_email',
            'country',
            'currency_id',
            'custom_label1',
            'custom_value1',
            'custom_label2',
            'custom_value2',
            'custom_client_label1',
            'custom_client_label2',
            'primary_color',
            'secondary_color',
            'hide_quantity',
            'hide_paid_to_date',
            'all_pages_header',
            'all_pages_footer',
            'custom_supply_label1',
            'custom_supply_label2',
            'pdf_email_attachment',
            'show_item_taxes',
            'custom_supply_text_label1',
            'custom_supply_text_label2',
            'custom_supply_item_label1',
            'custom_supply_item_label2',
            'supply_embed_documents',
            'page_size',
            'include_item_taxes_inline',
            'supply_fields',
        ]);

        foreach ($this->supply_items as $supplyItem) {
            $supplyItem->setVisible([
                'product_key',
                'notes',
                'custom_value1',
                'custom_value2',
                'cost',
                'qty',
                'tax_name1',
                'tax_rate1',
                'tax_name2',
                'tax_rate2',
            ]);
        }


        foreach ($this->documents as $document) {
            $document->setVisible([
                'public_id',
                'name',
            ]);
        }

        foreach ($this->expenses as $expense) {
            $expense->setVisible([
                'documents',
            ]);

            foreach ($expense->documents as $document) {
                $document->setVisible([
                    'public_id',
                    'name',
                ]);
            }
        }

        return $this;
    }

    /**
     * @return bool|\Recurr\RecurrenceCollection
     * @throws \Recurr\Exception\MissingData
     */
    public function getSchedule()
    {
        if (!$this->start_date || !$this->is_recurring || !$this->frequency_id) {
            return false;
        }

        $startDate = $this->getOriginal('last_sent_date') ?: $this->getOriginal('start_date');
        $startDate .= ' ' . $this->account->recurring_hour . ':00:00';
        $startDate = $this->account->getDateTime($startDate);
        $endDate = $this->end_date ? $this->account->getDateTime($this->getOriginal('end_date')) : null;
        $timezone = $this->account->getTimezone();

        $rule = $this->getRecurrenceRule();
        $rule = new \Recurr\Rule("{$rule}", $startDate, $endDate, $timezone);

        // Fix for months with less than 31 days
        $transformerConfig = new \Recurr\Transformer\ArrayTransformerConfig();
        $transformerConfig->enableLastDayOfMonthFix();

        $transformer = new \Recurr\Transformer\ArrayTransformer();
        $transformer->setConfig($transformerConfig);
        $dates = $transformer->transform($rule);

        if (count($dates) < 2) {
            return false;
        }

        return $dates;
    }

    /**
     * @return null
     */
    public function getNextSendDate()
    {
        if ($this->start_date && !$this->last_sent_date) {
            $startDate = $this->getOriginal('start_date') . ' ' . $this->account->recurring_hour . ':00:00';
            return $this->account->getDateTime($startDate);
        }

        if (!$schedule = $this->getSchedule()) {
            return null;
        }

        if (count($schedule) < 2) {
            return null;
        }

        return $schedule[1]->getStart();
    }

    /**
     * @param null $supply_date
     * @return mixed|null
     */
    public function getDueDate($supply_date = null){
        if(!$this->is_recurring) {
            return $this->due_date ? $this->due_date : null;
        }
        else{
            $now = time();
            if($supply_date) {
                // If $supply_date is specified, all calculations are based on that date
                if(is_numeric($supply_date)) {
                    $now = $supply_date;
                }
                else if(is_string($supply_date)) {
                    $now = strtotime($supply_date);
                }
                elseif ($supply_date instanceof \DateTime) {
                    $now = $supply_date->getTimestamp();
                }
            }

            if($this->due_date && $this->due_date != '0000-00-00'){
                // This is a recurring supply; we're using a custom format here.
                // The year is always 1998; January is 1st, 2nd, last day of the month.
                // February is 1st Sunday after, 1st Monday after, ..., through 4th Saturday after.
                $dueDateVal = strtotime($this->due_date);
                $monthVal = (int)date('n', $dueDateVal);
                $dayVal = (int)date('j', $dueDateVal);
                $dueDate = false;

                if($monthVal == 1) {// January; day of month
                    $currentDay = (int)date('j', $now);
                    $lastDayOfMonth = (int)date('t', $now);

                    $dueYear = (int)date('Y', $now);// This year
                    $dueMonth = (int)date('n', $now);// This month
                    $dueDay = $dayVal;// The day specified for the supply

                    if($dueDay > $lastDayOfMonth) {
                        // No later than the end of the month
                        $dueDay = $lastDayOfMonth;
                    }

                    if($currentDay >= $dueDay) {
                        // Wait until next month
                        // We don't need to handle the December->January wraparaound, since PHP handles month 13 as January of next year
                        $dueMonth++;

                        // Reset the due day
                        $dueDay = $dayVal;
                        $lastDayOfMonth = (int)date('t', mktime(0, 0, 0, $dueMonth, 1, $dueYear));// The number of days in next month

                        // Check against the last day again
                        if($dueDay > $lastDayOfMonth){
                            // No later than the end of the month
                            $dueDay = $lastDayOfMonth;
                        }
                    }

                    $dueDate = mktime(0, 0, 0, $dueMonth, $dueDay, $dueYear);
                }
                else if($monthVal == 2) {// February; day of week
                    $ordinals = ['first', 'second', 'third', 'fourth'];
                    $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

                    $ordinalIndex = ceil($dayVal / 7) - 1;// 1-7 are "first"; 8-14 are "second", etc.
                    $dayOfWeekIndex = ($dayVal - 1) % 7;// 1,8,15,22 are Sunday, 2,9,16,23 are Monday, etc.
                    $dayStr = $ordinals[$ordinalIndex] . ' ' . $daysOfWeek[$dayOfWeekIndex];// "first sunday", "first monday", etc.

                    $dueDate = strtotime($dayStr, $now);
                }

                if($dueDate) {
                    return date('Y-m-d', $dueDate);// SQL format
                }
            }
            else if ($this->client->payment_terms != 0) {
                // No custom due date set for this supply; use the client's payment terms
                $days = $this->client->payment_terms;
                if ($days == -1) {
                    $days = 0;
                }
                return date('Y-m-d', strtotime('+'.$days.' day', $now));
            }
        }

        // Couldn't calculate one
        return null;
    }

    /**
     * @param int $min
     * @param int $max
     * @return null
     */
    public function getPrettySchedule($min = 1, $max = 10)
    {
        if (!$schedule = $this->getSchedule($max)) {
            return null;
        }

        $dates = [];

        for ($i=$min; $i<min($max, count($schedule)); $i++) {
            $date = $schedule[$i];
            $dateStart = $date->getStart();
            $date = $this->account->formatDate($dateStart);
            $dueDate = $this->getDueDate($dateStart);

            if($dueDate) {
                $date .= ' <small>(' . trans('texts.due') . ' ' . $this->account->formatDate($dueDate) . ')</small>';
            }

            $dates[] = $date;
        }

        return implode('<br/>', $dates);
    }

    /**
     * @return string
     */
    private function getRecurrenceRule()
    {
        $rule = '';

        switch ($this->frequency_id) {
            case FREQUENCY_WEEKLY:
                $rule = 'FREQ=WEEKLY;';
                break;
            case FREQUENCY_TWO_WEEKS:
                $rule = 'FREQ=WEEKLY;INTERVAL=2;';
                break;
            case FREQUENCY_FOUR_WEEKS:
                $rule = 'FREQ=WEEKLY;INTERVAL=4;';
                break;
            case FREQUENCY_MONTHLY:
                $rule = 'FREQ=MONTHLY;';
                break;
            case FREQUENCY_THREE_MONTHS:
                $rule = 'FREQ=MONTHLY;INTERVAL=3;';
                break;
            case FREQUENCY_SIX_MONTHS:
                $rule = 'FREQ=MONTHLY;INTERVAL=6;';
                break;
            case FREQUENCY_ANNUALLY:
                $rule = 'FREQ=YEARLY;';
                break;
        }

        if ($this->end_date) {
            $rule .= 'UNTIL=' . $this->getOriginal('end_date');
        }

        return $rule;
    }

    /*
    public function shouldSendToday()
    {
        if (!$nextSendDate = $this->getNextSendDate()) {
            return false;
        }

        return $this->account->getDateTime() >= $nextSendDate;
    }
    */

    /**
     * @return bool
     */
    public function shouldSendToday()
    {
        if ( ! $this->user->confirmed) {
            return false;
        }

        if ( ! $this->start_date || strtotime($this->start_date) > strtotime('now')) {
            return false;
        }

        if ($this->end_date && strtotime($this->end_date) < strtotime('now')) {
            return false;
        }

        $dayOfWeekToday = date('w');
        $dayOfWeekStart = date('w', strtotime($this->start_date));

        $dayOfMonthToday = date('j');
        $dayOfMonthStart = date('j', strtotime($this->start_date));

        if (!$this->last_sent_date) {
            return true;
        } else {
            $date1 = new DateTime($this->last_sent_date);
            $date2 = new DateTime();
            $diff = $date2->diff($date1);
            $daysSinceLastSent = $diff->format('%a');
            $monthsSinceLastSent = ($diff->format('%y') * 12) + $diff->format('%m');

            if ($daysSinceLastSent == 0) {
                return false;
            }
        }

        switch ($this->frequency_id) {
            case FREQUENCY_WEEKLY:
                return $daysSinceLastSent >= 7;
            case FREQUENCY_TWO_WEEKS:
                return $daysSinceLastSent >= 14;
            case FREQUENCY_FOUR_WEEKS:
                return $daysSinceLastSent >= 28;
            case FREQUENCY_MONTHLY:
                return $monthsSinceLastSent >= 1;
            case FREQUENCY_THREE_MONTHS:
                return $monthsSinceLastSent >= 3;
            case FREQUENCY_SIX_MONTHS:
                return $monthsSinceLastSent >= 6;
            case FREQUENCY_ANNUALLY:
                return $monthsSinceLastSent >= 12;
            default:
                return false;
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getPDFString()
    {
        if (!env('PHANTOMJS_CLOUD_KEY')) {
            return false;
        }

        $invitation = $this->invitations[0];
        $link = $invitation->getLink('view', true);
        $key = env('PHANTOMJS_CLOUD_KEY');

        if (Utils::isNinjaDev()) {
            $link = env('TEST_LINK');
        }

        $url = "http://api.phantomjscloud.com/api/browser/v2/{$key}/?request=%7Burl:%22{$link}?phantomjs=true%22,renderType:%22html%22%7D";

        $pdfString = file_get_contents($url);
        $pdfString = strip_tags($pdfString);

        if ( ! $pdfString || strlen($pdfString) < 200) {
            Utils::logError("PhantomJSCloud - failed to create pdf: {$pdfString}");
            return false;
        }

        return Utils::decodePDF($pdfString);
    }

    /**
     * @param $supplyItem
     * @param $supplyTotal
     * @return float|int
     */
    public function getItemTaxable($supplyItem, $supplyTotal)
    {
        $total = $supplyItem->qty * $supplyItem->cost;

        if ($this->discount > 0) {
            if ($this->is_amount_discount) {
                $total -= $supplyTotal ? ($total / $supplyTotal * $this->discount) : 0;
            } else {
                $total *= (100 - $this->discount) / 100;
                $total = round($total, 2);
            }
        }

        return $total;
    }

    /**
     * @return float|int|mixed
     */
    public function getTaxable()
    {
        $total = 0;

        foreach ($this->supply_items as $supplyItem) {
            $total += $supplyItem->qty * $supplyItem->cost;
        }

        if ($this->discount > 0) {
            if ($this->is_amount_discount) {
                $total -= $this->discount;
            } else {
                $total *= (100 - $this->discount) / 100;
                $total = round($total, 2);
            }
        }

        if ($this->custom_value1 && $this->custom_taxes1) {
            $total += $this->custom_value1;
        }

        if ($this->custom_value2 && $this->custom_taxes2) {
            $total += $this->custom_value2;
        }

        return $total;
    }

    // if $calculatePaid is true we'll loop through each payment to
    // determine the sum, otherwise we'll use the cached paid_to_date amount
    /**
     * @param bool $calculatePaid
     * @return array
     */
    public function getTaxes($calculatePaid = false)
    {
        $taxes = [];
        $taxable = $this->getTaxable();
        $paidAmount = $this->getAmountPaid($calculatePaid);

        if ($this->tax_name1) {
            $supplyTaxAmount = round($taxable * ($this->tax_rate1 / 100), 2);
            $supplyPaidAmount = floatVal($this->amount) && $supplyTaxAmount ? ($paidAmount / $this->amount * $supplyTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name1, $this->tax_rate1, $supplyTaxAmount, $supplyPaidAmount);
        }

        if ($this->tax_name2) {
            $supplyTaxAmount = round($taxable * ($this->tax_rate2 / 100), 2);
            $supplyPaidAmount = floatVal($this->amount) && $supplyTaxAmount ? ($paidAmount / $this->amount * $supplyTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name2, $this->tax_rate2, $supplyTaxAmount, $supplyPaidAmount);
        }

        foreach ($this->supply_items as $supplyItem) {
            $itemTaxAmount = $this->getItemTaxable($supplyItem, $taxable);

            if ($supplyItem->tax_name1) {
                $itemTaxAmount = round($taxable * ($supplyItem->tax_rate1 / 100), 2);
                $itemPaidAmount = floatVal($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $supplyItem->tax_name1, $supplyItem->tax_rate1, $itemTaxAmount, $itemPaidAmount);
            }

            if ($supplyItem->tax_name2) {
                $itemTaxAmount = round($taxable * ($supplyItem->tax_rate2 / 100), 2);
                $itemPaidAmount = floatVal($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $supplyItem->tax_name2, $supplyItem->tax_rate2, $itemTaxAmount, $itemPaidAmount);
            }
        }

        return $taxes;
    }

    /**
     * @param $taxes
     * @param $name
     * @param $rate
     * @param $amount
     * @param $paid
     */
    private function calculateTax(&$taxes, $name, $rate, $amount, $paid)
    {
        if ( ! $amount) {
            return;
        }

        $amount = round($amount, 2);
        $paid = round($paid, 2);
        $key = $rate . ' ' . $name;

        if ( ! isset($taxes[$key])) {
            $taxes[$key] = [
                'name' => $name,
                'rate' => $rate+0,
                'amount' => 0,
                'paid' => 0
            ];
        }

        $taxes[$key]['amount'] += $amount;
        $taxes[$key]['paid'] += $paid;
    }

    /**
     * @return bool
     */
    public function hasDocuments(){
        if(count($this->documents))return true;
        return $this->hasExpenseDocuments();
    }

    /**
     * @return bool
     */
    public function hasExpenseDocuments(){
        foreach($this->expenses as $expense){
            if(count($expense->documents))return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getAutoBillEnabled() {
        if (!$this->is_recurring) {
            $recurSupply = $this->recurring_supply;
        } else {
            $recurSupply = $this;
        }

        if (!$recurSupply) {
            return false;
        }

        return $recurSupply->auto_bill == AUTO_BILL_ALWAYS || ($recurSupply->auto_bill != AUTO_BILL_OFF && $recurSupply->client_enable_auto_bill);
    }
}


