<?php namespace App\Models\Main;

use Eloquent;

/**
 * Class PaymentType
 */
class PaymentType extends ModelDBMain
{

    protected $connection = 'main';    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gatewayType()
    {
        return $this->belongsTo('App\Models\Main\GatewayType');
    }

    public static function getAll()
    {
        // Obtener todos los registros de PaymentType ordenados primero c
        $paymentTypes = PaymentType::orderByRaw("CASE WHEN name LIKE 'C%' THEN 0 ELSE 1 END, name")
            ->get();

        return $paymentTypes;
    }
}
