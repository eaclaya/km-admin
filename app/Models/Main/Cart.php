<?php namespace App\Models\Main;

use Utils;
use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Cart
 */
class Cart extends ModelDBMain
{

    protected $connection = 'main';

    /**
     * @return string
     */
    public function getRoute()
    {

        return "/carts/{$this->public_id}/edit";
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

    public function employee()
    {
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
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

     public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice')->withTrashed();
    }
    /**
     * @return mixed
     */
    public function cart_items()
    {
        return $this->hasMany('App\Models\Main\CartItem');
    }

    /**
     * @return bool
     */
    public function isQuote() {
        return false;
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
        return $this->is_recurring ? trans('texts.recurring') : $this->cart_number;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        $entityType = $this->getEntityType();
        return trans("texts.$entityType") . '_' . $this->cart_number . '.pdf';
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

        return ENTITY_REFUND;
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


}
