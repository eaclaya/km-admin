<?php

namespace App\Models\Main;

use App\Traits\UpdatesWithEvent;

/**
 * Class Company
 */
class ConditionsSpecialNegotiation extends ModelDBMain
{
    use UpdatesWithEvent;

    protected $connection = 'main';

    protected $dates = ['deleted_at'];

    protected $table = 'conditions_special_negotiation';

    protected $casts = [
        'amount_range' => 'array',
        'penalties' => 'array',
    ];

    protected $fillable = [
        'id',
        'condition_range',
        'amount_range',
        'normal_discount',
        'limit_discount',
        'penalties',
    ];

    public function getAmountRangeStringAttribute()
    {
        $range = $this->amount_range;
        $min = (int) $range['min'];
        $max = (int) $range['max'];

        $minK = floor($min / 1000);
        $maxK = floor($max / 1000);
        $minK = $minK.'k ~ ';
        $maxK = $maxK > 0 ? $maxK.'k' : '>';

        return "$minK"."$maxK";
    }

    public function getPenalty($value)
    {
        $penalties = $this->penalties;
        $penalty = 0;

        foreach ($penalties as $range => $amount) {
            [$min, $max] = explode('-', $range);
            if (($min === '0' && $value <= $max) || ($value >= $min && $value <= $max)) {
                $penalty = $amount;
                break;
            }
        }

        return $penalty;
    }

    public function getDiscountStringAttribute()
    {
        return '<span class="text-success">'.$this->normal_discount.'%</span>'.' ó '.'<span class="text-danger">'.$this->limit_discount.'%</span>';
    }

    public function getConditionRangeArrayAttribute()
    {
        return explode('-', (string) $this->condition_range);
    }
}
