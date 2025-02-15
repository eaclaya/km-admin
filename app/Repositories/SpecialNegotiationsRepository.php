<?php

namespace App\Repositories;

use App\Models\Main\Client;
use App\Models\Main\ConditionsSpecialNegotiation;
use App\Models\Main\DiscountQuota;
use App\Models\Main\PaymentQuota;
use App\Models\Main\Quota;
use App\Models\Main\RefundQuota;
use App\Models\Main\SpecialNegotiation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class SpecialNegotiationsRepository
{
    public function createSpecialNegotiation($data): ?SpecialNegotiation
    {
        $invoices_ids = $data['invoice_id'];
        unset($data['invoice_id']);
        unset($data['_token']);
        $data['estimated_percentage'] = $this->calculatePorcentStimed(
            $data['conditions_special_negotiation_id'],
            $invoices_ids
        );
        $data['is_document'] = $this->calculateDocument($data['client_id']);
        $negotiation = SpecialNegotiation::create($data);
        $negotiation->invoices()->attach($invoices_ids);

        return $negotiation;
    }

    public function updateSpecialNegotiation($id, $data): ?SpecialNegotiation
    {
        $invoices_ids = $data['invoice_id'];
        unset($data['invoice_id']);
        unset($data['_token']);
        $data['estimated_percentage'] = $this->calculatePorcentStimed(
            $data['conditions_special_negotiation_id'],
            $invoices_ids
        );
        $negotiation = SpecialNegotiation::find($id);
        $data['is_document'] = $this->calculateDocument($negotiation->client_id);
        $negotiation->activateTracking();
        $negotiation->setReason($data['reason']);
        unset($data['reason']);
        $negotiation->update($data);
        $negotiation->invoices()->sync($invoices_ids);

        return $negotiation;
    }

    public function firstShowSpecialNegotiation($id): ?SpecialNegotiation
    {
        $negotiation = SpecialNegotiation::where('id', $id)->with([
            'invoices:id,invoice_number,amount,discount_negotiations',
            'route:id,name', 'account:id,name', 'employee:id,first_name,last_name',
            'client:id,name,company_name,phone,work_phone,address1',
            'quotas', 'quotas.invoices:id,invoice_number,amount,discount_negotiations',
            'quotas.payments', 'quotas.discounts', 'quotas.refunds', 'condition',
        ])->first();

        return $negotiation;
    }

    public function createQuotas($data, $negosation_id)
    {
        return DB::transaction(function () use ($data, $negosation_id) {
            $oldQuotas = Quota::where('special_negotiations_id', $negosation_id)->orderBy('id', 'asc')->get();
            $oldQuotasCount = $oldQuotas->count();
            $dataCount = count($data);

            for ($i = 0; $i < max($oldQuotasCount, $dataCount); $i++) {
                if ($i < $dataCount) {
                    $value = $data[$i];
                    $invoices = $value['invoice_id'];
                    unset($value['invoice_id']);
                    $value['status'] = 0;

                    if ($i < $oldQuotasCount) {
                        $quota = $oldQuotas[$i];
                        if (isset($quota->status)) {
                            $value['status'] = $quota->status;
                        } else {
                            $value['status'] = 0;
                        }
                        $quota->update($value);
                        $quota->invoices()->sync($invoices);
                        $this->paymentCalculate($quota->id);
                    } else {
                        $quota = Quota::create($value);
                        $quota->invoices()->sync($invoices);
                    }
                } else {
                    $quota = $oldQuotas[$i];
                    foreach ($quota->discounts as $discount) {
                        $this->clearDiscountInvoicetoUpdate($discount);
                        $discount->delete();
                    }
                    $quota->payments()->delete();
                    $quota->refunds()->delete();
                    $quota->invoices()->detach();
                    $quota->delete();
                }
            }

            $quotas = Quota::where('special_negotiations_id', $negosation_id)->get();

            return $quotas;
        });
    }

    public function updateQuota($id, $data)
    {
        $quota = Quota::find($id);
        if (! isset($quota)) {
            return false;
        }
        $invoices = $data['invoice_id'];
        unset($data['_token']);
        unset($data['invoice_id']);
        $quota->activateTracking();
        $quota->setReason($data['reason']);
        unset($data['reason']);
        $quota->update($data);
        $quota->invoices()->sync($invoices);

        $this->calculateQuotaStatus($id);

        return $quota;
    }

    public function createPayment($data)
    {
        unset($data['_token']);
        $quota_id = $data['quota_id'];
        $quota = Quota::find($quota_id);
        $monthlyPayment = $quota->monthly_payment;
        $days = Carbon::now()->diffInDays(Carbon::parse($quota->credit_payment_at)) + 1;
        $is_overdue = $days <= 0 ? true : false;
        $data['mount_balance_total'] = 0;
        $data['final_balance'] = 0;
        $data['overdue_balance'] = $is_overdue ? $monthlyPayment : 0;

        $payment = PaymentQuota::create($data);
        $this->paymentCalculate($quota_id);

        $this->calculateQuotaStatus($quota_id, $payment->final_balance);

        return $payment;
    }

    public function updatePayment($id, $data)
    {
        $payment = PaymentQuota::find($id);
        if (! isset($payment)) {
            return false;
        }
        unset($data['_token']);
        $payment->activateTracking();
        $payment->setReason($data['reason']);
        unset($data['reason']);
        $payment->update($data);

        $quota_id = $payment->quota_id;
        $this->paymentCalculate($quota_id);

        $this->calculateQuotaStatus($quota_id, $payment->final_balance);

        return $payment;
    }

    public function destroyPayment($data, $id)
    {
        $payment = PaymentQuota::find($id);
        if (! isset($payment)) {
            return false;
        }
        try {
            unset($data['_token']);
            $payment->activateTracking();
            $payment->setReason($data['reason']);
            unset($data['reason']);
            $payment->save();

            $quota_id = $payment->quota_id;
            $payment->delete();

            $this->paymentCalculate($quota_id);
            $this->calculateQuotaStatus($quota_id, null);

            return true;
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }

    public function createRefund($data)
    {
        unset($data['_token']);
        $quota_id = $data['quota_id'];
        $quota = Quota::find($quota_id);
        $days = Carbon::now()->diffInDays(Carbon::parse($quota->credit_payment_at)) + 1;
        $is_overdue = $days <= 0 ? true : false;

        $paymentCuota = PaymentQuota::where('quota_id', $quota_id)
            ->select('final_balance')
            ->orderBy('id', 'desc')
            ->first();

        $monthlyRefund = 0;
        if (isset($paymentCuota)) {
            $monthlyRefund = $paymentCuota->final_balance;
        }

        $data['mount_balance_total'] = 0;
        $data['final_balance'] = 0;
        $data['overdue_balance'] = $is_overdue ? $monthlyRefund : 0;

        $refund = RefundQuota::create($data);
        $this->refundCalculate($quota_id);

        $this->calculateQuotaStatus($quota_id, $refund->final_balance);

        return $refund;
    }

    public function updateRefund($id, $data)
    {
        $refund = RefundQuota::find($id);

        if (! isset($refund)) {
            return false;
        }
        unset($data['_token']);
        $refund->activateTracking();
        $refund->setReason($data['reason']);
        unset($data['reason']);
        $refund->update($data);

        $quota_id = $refund->quota_id;
        $this->refundCalculate($quota_id);

        $this->calculateQuotaStatus($quota_id, $refund->final_balance);

        return $refund;
    }

    public function destroyRefund($data, $id)
    {
        $refund = RefundQuota::find($id);
        if (! isset($refund)) {
            return false;
        }
        try {
            unset($data['_token']);
            $refund->activateTracking();
            $refund->setReason($data['reason']);
            unset($data['reason']);
            $refund->save();

            $quota_id = $refund->quota_id;
            $refund->delete();

            $this->refundCalculate($quota_id);
            $this->calculateQuotaStatus($quota_id, null);

            return true;
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }

    public function createDiscount($data)
    {
        unset($data['_token']);
        $quota_id = $data['quota_id'];
        $final_balance = $this->calculateFinalBalance($quota_id);
        $data['final_balance'] = $final_balance - $data['discount_applied'];
        $discount = DiscountQuota::create($data);
        $this->insertDiscountInvoice($discount);
        $this->calculateQuotaStatus($quota_id, $discount->final_balance);

        return true;
    }

    public function updateDiscount($id, $data)
    {
        $discount = DiscountQuota::find($id);
        if (! isset($discount)) {
            return false;
        }
        $this->clearDiscountInvoicetoUpdate($discount);
        $quota_id = $discount->quota_id;
        $final_balance = $this->calculateFinalBalance($quota_id, $id);

        $data['final_balance'] = $final_balance - $data['discount_applied'];

        unset($data['_token']);
        $discount->activateTracking();
        $discount->setReason($data['reason']);
        unset($data['reason']);
        $discount->update($data);
        $newDiscount = DiscountQuota::find($id);
        $this->insertDiscountInvoice($newDiscount);

        $this->calculateQuotaStatus($quota_id, $discount->final_balance);

        return $discount;
    }

    public function calculateDiscountNegotiation($negotiation)
    {
        $discounts = $negotiation->discounts();
        $porcentQuotasDiscount = $discounts->sum('porcent_quotas_discount');
        $discountsCount = $discounts->count();
        if ($porcentQuotasDiscount > 0 && $discountsCount > 0) {
            $negotiation->negotiations_discount = $porcentQuotasDiscount / $discountsCount;
            $negotiation->save();
        }
    }

    public function insertDiscountInvoice($discount)
    {
        $discount_negotiation = $discount->discount_applied;
        $invoice = $discount->invoice;

        $invoice->discount_negotiations += $discount_negotiation;
        $invoice->discount += $discount_negotiation;
        $invoice->amount -= $discount_negotiation;
        $invoice->replacement_amount -= $discount_negotiation;
        $invoice->save();

        $negotiation = $discount->special_negotiation;
        $newDiscount = $discount->porcent_quotas_discount;
        $this->calculateDiscountNegotiation($negotiation, $newDiscount);

    }

    public function clearDiscountInvoicetoUpdate($discount)
    {
        $discount_negotiation = $discount->discount_applied;
        $invoice = $discount->invoice;

        $negotiation = $discount->special_negotiation;
        $negotiation->negotiations_discount -= $discount->porcent_quotas_discount;
        $negotiation->save();

        $invoice->discount_negotiations -= $discount_negotiation;
        $invoice->discount -= $discount_negotiation;
        $invoice->amount += $discount_negotiation;
        $invoice->replacement_amount += $discount_negotiation;
        $invoice->save();
    }

    public function setCreditRecord($id, $data)
    {
        $negotiation = SpecialNegotiation::find($id);
        if (! isset($negotiation)) {
            return false;
        }
        $negotiation->activateTracking();
        $negotiation->setReason('credit_record');
        $negotiation->update($data);

        return $negotiation;
    }

    public function setDocument($id)
    {
        $negotiation = SpecialNegotiation::find($id);
        if (! isset($negotiation)) {
            return false;
        }
        $data['is_document'] = $this->calculateDocument($negotiation->client_id);
        $negotiation->activateTracking();
        $negotiation->setReason('credit_record');
        $negotiation->update($data);

        return $negotiation;
    }

    public function calculateFinalBalance($quota_id, $not_discount_id = null)
    {
        $quota = Quota::find($quota_id);
        $discount = $quota->discounts()->select('final_balance')->when($not_discount_id, function ($query, $not_discount_id) {
            return $query->whereNot('id', $not_discount_id);
        })->latest()->first();
        $final_balance_discount = isset($discount) ? $discount->final_balance : 0;
        if (isset($final_balance_discount) && $final_balance_discount > 0) {
            return $final_balance_discount;
        }
        $refund = $quota->refunds()->select('final_balance')->latest()->first();
        $final_balance_refund = isset($refund) ? $refund->final_balance : 0;
        if (isset($final_balance_refund) && $final_balance_refund > 0) {
            return $final_balance_refund;
        }
        $payment = $quota->payments()->select('final_balance')->latest()->first();
        $final_balance_payment = isset($payment) ? $payment->final_balance : 0;
        if (isset($final_balance_payment) && $final_balance_payment > 0) {
            return $final_balance_payment;
        }

        return 0;
    }

    public function calculateQuotaStatus($quota_id, $final_balance = null)
    {
        $quota = Quota::find($quota_id);
        if (is_null($final_balance)) {
            $final_balance = $this->calculateFinalBalance($quota_id);
        }
        if ($final_balance <= 0) {
            $quota->status = 1;
            $quota->save();

            return;
        }
        $days = Carbon::now()->diffInDays(Carbon::parse($quota->credit_payment_at)) + 1;
        if ($days <= 0) {
            $quota->status = 2;
            $quota->save();
        }
    }

    public function calculateNegotiationStatus($negotiationId)
    {
        $quotas = Quota::where('special_negotiations_id', $negotiationId)->where('status', '<', 1)->count();
        if (! isset($quotas) || $quotas == 0) {
            $negotiation = SpecialNegotiation::find($negotiationId);
            $negotiation->status = 1;
            $negotiation->activateTracking();
            $negotiation->save();
        }
    }

    public function paymentCalculate($quota_id)
    {
        $monthlyPayment = Quota::where('id', $quota_id)->first()->monthly_payment;

        $payments = PaymentQuota::where('quota_id', $quota_id)
            ->select('id', 'mount_balance', 'mount_balance_total', 'final_balance')
            ->orderBy('id', 'asc')
            ->get();

        $lastPaymentBalanceTotal = 0;
        $lastFinalBalance = $monthlyPayment;

        foreach ($payments as $payment) {
            $payment->mount_balance_total = floatval($lastPaymentBalanceTotal + $payment->mount_balance);
            $payment->final_balance = floatval($lastFinalBalance - $payment->mount_balance);
            $payment->save();

            $lastPaymentBalanceTotal = $payment->mount_balance_total;
            $lastFinalBalance = $payment->final_balance;
        }
        $this->refundCalculate($quota_id);
    }

    public function refundCalculate($quota_id)
    {
        $lastPayment = PaymentQuota::where('quota_id', $quota_id)
            ->select('final_balance', 'mount_balance_total')
            ->orderBy('id', 'desc')
            ->first();

        $lastRefundQuotaBalanceTotal = 0;
        $monthlyRefundQuota = 0;
        if (isset($lastPayment)) {
            $monthlyRefundQuota = $lastPayment->final_balance;
            $lastRefundQuotaBalanceTotal = $lastPayment->mount_balance_total;
        } else {
            $monthlyRefundQuota = Quota::where('id', $quota_id)->first()->monthly_payment;
        }

        $refundQuotas = RefundQuota::where('quota_id', $quota_id)
            ->select('id', 'mount_balance', 'mount_balance_total', 'final_balance')
            ->orderBy('id', 'asc')
            ->get();

        $lastFinalBalance = $monthlyRefundQuota;

        foreach ($refundQuotas as $refundQuota) {
            $refundQuota->mount_balance_total = floatval($lastRefundQuotaBalanceTotal - $refundQuota->mount_balance);
            $refundQuota->final_balance = floatval($lastFinalBalance - $refundQuota->mount_balance);
            $refundQuota->save();

            $lastRefundQuotaBalanceTotal = $refundQuota->mount_balance_total;
            $lastFinalBalance = $refundQuota->final_balance;
        }
    }

    public function setConditionInToNegotiation($conditionsSpecialNegotiationId, $negotiationId)
    {
        $specialNegotiation = SpecialNegotiation::find($negotiationId);

        if ($specialNegotiation->conditions_special_negotiation_id == $conditionsSpecialNegotiationId) {
            return ConditionsSpecialNegotiation::find($conditionsSpecialNegotiationId);
        }
        $specialNegotiation->conditions_special_negotiation_id = $conditionsSpecialNegotiationId;
        $estimatedPercentage = $this->calculatePorcentStimed(
            $conditionsSpecialNegotiationId,
            $specialNegotiation->invoices->pluck('id')->toArray()
        );
        $specialNegotiation->estimated_percentage = $estimatedPercentage;
        $specialNegotiation->save();

        return ConditionsSpecialNegotiation::find($conditionsSpecialNegotiationId);
    }

    public function calculatePorcentStimed($condition_id, $invoices_ids)
    {
        $condition = ConditionsSpecialNegotiation::where('id', $condition_id)->first();
        $items = DB::connection('main')->table('invoice_items')
            ->whereIn('invoice_items.invoice_id', $invoices_ids)
            ->join('products', function ($join) {
                $join->on('products.product_key', '=', 'invoice_items.product_key')
                    ->where('products.account_id', 17);
            })
            ->join('categories', function ($join) {
                $join->on('categories.category_id', '=', 'products.category_id');
            })
            ->select('invoice_items.product_key', 'categories.name as category_name')
            ->get();
        $limit = false;
        $categories_limits = ['cascos', 'llantas'];
        foreach ($items as $item) {
            $catagoryLowerCase = strtolower($item->category_name);
            if (in_array($catagoryLowerCase, $categories_limits)) {
                $limit = true;
                break;
            }
        }
        if ($limit) {
            return $condition->limit_discount;
        }

        return $condition->normal_discount;
    }

    public function calculateDocument($cliend_id)
    {
        $client = Client::find($cliend_id);
        if (isset($client)) {
            $a = json_decode($client->extra_attributes, true);
            $adjunts = isset($a['adjunts']) ? $a['adjunts'] : null;
            if (! isset($adjunts)) {
                return 0;
            }
            if (! isset($adjunts['revision_historial_credito']) && $client->is_credit) {
                return 1;
            }
            if (! isset($adjunts['letra_cambio_firmada']) && $client->is_credit) {
                return 1;
            }
            if (! isset($adjunts['identidad'])) {
                return 1;
            }
            if (! isset($adjunts['rtn'])) {
                return 1;
            }
            if (! isset($adjunts['recibo_servicio_publico']) && $client->is_credit) {
                return 1;
            }
            if (! isset($adjunts['croquis']) && $client->is_credit) {
                return 1;
            }
            if (! isset($adjunts['foto_actividad_negocio']) && $client->is_credit) {
                return 1;
            }

            return 2;
        }

        return 0;
    }
}
