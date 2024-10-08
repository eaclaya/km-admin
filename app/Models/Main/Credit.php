<?php namespace App\Models\Main;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\CreditWasCreated;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Credit
 */
class Credit extends ModelDBMain
{

    protected $connection = 'main';
    use SoftDeletes;
    use PresentableTrait;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\CreditPresenter';

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
    public function invoice()
    {
        return $this->belongsTo('App\Models\Main\Invoice')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Main\Client')->withTrashed();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CREDIT;
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function apply($amount)
    {
        if ($amount > $this->balance) {
            $applied = $this->balance;
            $this->balance = 0;
        } else {
            $applied = $amount;
            $this->balance = $this->balance - $amount;
        }

        $this->save();

        return $applied;
    }
}

Credit::creating(function ($credit) {

});

Credit::created(function ($credit) {
    event(new CreditWasCreated($credit));
});
