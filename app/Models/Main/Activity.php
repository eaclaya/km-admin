<?php namespace App\Models\Main;

use Auth;
use Eloquent;

/**
 * Class Activity
 */
class Activity extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @param $query
     * @return mixed
     */
    public function scopeScope($query)
    {
        return $query->whereAccountId(Auth::user()->account_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
    public function contact()
    {
        return $this->belongsTo('App\Models\Main\Contact')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function credit()
    {
        return $this->belongsTo('App\Models\Main\Credit')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function payment()
    {
        return $this->belongsTo('App\Models\Main\Payment')->withTrashed();
    }

    public function task()
    {
        return $this->belongsTo('App\Models\Main\Task')->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo('App\Models\Main\Expense')->withTrashed();
    }

    public function key()
    {
        return sprintf('%s-%s-%s', $this->activity_type_id, $this->client_id, $this->created_at->timestamp);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        $activityTypeId = $this->activity_type_id;
        $account = $this->account;
        $client = $this->client;
        $user = $this->user;
        $invoice = $this->invoice;
        $contactId = $this->contact_id;
        $payment = $this->payment;
        $credit = $this->credit;
        $expense = $this->expense;
        $isSystem = $this->is_system;
        $task = $this->task;

        $data = [
            'client' => $client ? link_to($client->getRoute(), $client->getDisplayName()) : null,
            //'user' => $isSystem ? '<i>' . trans('texts.system') . '</i>' : $user->getDisplayName(),
            'invoice' => $invoice ? link_to($invoice->getRoute(), $invoice->getDisplayName()) : null,
            'quote' => $invoice ? link_to($invoice->getRoute(), $invoice->getDisplayName()) : null,
//            'contact' => $contactId ? $client->getDisplayName() : $user->getDisplayName(),
            'payment' => $payment ? $payment->transaction_reference : null,
            'payment_amount' => $payment ? $account->formatMoney($payment->amount, $payment) : null,
            'adjustment' => $this->adjustment ? $account->formatMoney($this->adjustment, $this) : null,
            'credit' => $credit ? $account->formatMoney($credit->amount, $client) : null,
            'task' => $task ? link_to($task->getRoute(), substr($task->description, 0, 30).'...') : null,
            'expense' => $expense ? link_to($expense->getRoute(), substr($expense->public_notes, 0, 30).'...') : null,
        ];

        return trans("texts.activity_{$activityTypeId}", $data);
    }
}
