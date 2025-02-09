<?php namespace App\Models\Main;


/**
 * Class Invoice
 */
class InvoiceHistory extends ModelDBMain
{

    protected $connection = 'main';
    protected $dates = ['deleted_at'];

    protected $table = 'invoices_history';
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    protected $fillable = [
    	"client_id",
"user_id",
"real_user_id",
"real_user_name",
"employee_id",
"account_id",
"invoice_status_id",
"created_at",
"updated_at",
"deleted_at",
"invoice_number",
"billing_id",
"discount",
"po_number",
"invoice_date",
"due_date",
"terms",
"public_notes",
"is_deleted",
"is_recurring",
"frequency_id",
"start_date",
"end_date",
"last_sent_date",
"recurring_invoice_id",
"tax_name1",
"tax_rate1",
"subtotal",
"amount",
"balance",
"total_refunded",
"commission",
"public_id",
"invoice_design_id",
"invoice_type_id",
"quote_id",
"quote_invoice_id",
"custom_value1",
"custom_value2",
"custom_taxes1",
"custom_taxes2",
"is_amount_discount",
"invoice_footer",
"partial",
"has_tasks",
"auto_bill",
"custom_text_value1",
"custom_text_value2",
"has_expenses",
"tax_name2",
"tax_rate2",
"client_enable_auto_bill",
"seller_id",
"is_credit",
"credit_days",
"tax_detailed",
"daily_cash",
"commission_paid",
"commission_counted",
"real_employee_id"
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Main\Account');
    }


    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Main\User')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

    public function employee()
    {
	return $this->belongsTo('App\Models\Main\Employee');
    }

    public function real_employee()
    {
	    return $this->belongsTo('App\Models\Main\Employee','real_employee_id', 'id')
            ->selectRaw("CONCAT(first_name,' ',last_name) as name, first_name, last_name");
    }

    /**
     * @return mixed
     */
    public function invoice_items()
    {
        return $this->hasMany('App\Models\Main\InvoiceItemHistory', 'invoice_id', 'id')->orderBy('id');
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
    public function invoice_status()
    {
        return $this->belongsTo('App\Models\Main\InvoiceStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice_design()
    {
        return $this->belongsTo('App\Models\Main\InvoiceDesign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public  function payments()
    {
        return $this->hasMany('App\Models\Main\PaymentHistory', 'invoice_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recurring_invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recurring_invoices()
    {
        return $this->hasMany('App\Models\Main\Invoice', 'recurring_invoice_id');
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
    public function expenses()
    {
        return $this->hasMany('App\Models\Main\Expense','invoice_id','id')->withTrashed();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeInvoices($query)
    {
        return $query->where('invoice_type_id', '=', INVOICE_TYPE_STANDARD)
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
        return $query->where('invoice_type_id', '=', INVOICE_TYPE_QUOTE)
                     ->where('is_recurring', '=', false);
    }

    /**
     * @param $query
     * @param $typeId
     * @return mixed
     */
    public function scopeInvoiceType($query, $typeId)
    {
        return $query->where('invoice_type_id', '=', $typeId);
    }

    /**
     * @param $typeId
     * @return bool
     */
    public function isType($typeId) {
        return $this->invoice_type_id == $typeId;
    }

    /**
     * @return bool
     */
    public function isQuote() {
        return $this->isType(INVOICE_TYPE_QUOTE);
    }

    /**
     * @return bool
     */
    public function isInvoice() {
        return $this->isType(INVOICE_TYPE_STANDARD) && ! $this->is_recurring;
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
     * @return mixed
     */
    public function getEntityType()
    {

        return $this->isType(INVOICE_TYPE_QUOTE) ? ENTITY_QUOTE : ENTITY_INVOICE;
    }

    public function subEntityType()
    {
        if ($this->is_recurring) {
            return ENTITY_RECURRING_INVOICE;
        } else {
            return $this->getEntityType();
        }
    }


    
}

