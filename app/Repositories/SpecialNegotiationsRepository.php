<?php

namespace App\Repositories;

use App\Models\Main\SpecialNegotiation;
use App\Models\Main\DiscountQuota;
use App\Models\Main\PaymentQuota;
use App\Models\Main\RefundQuota;
use App\Models\Main\Quota;

use Carbon\Carbon;


class SpecialNegotiationsRepository
{

    public function paymentCalculate($quota_id)
    {
        $monthlyPayment = Quota::where('id',$quota_id)->first()->monthly_payment;

        $payments = PaymentQuota::where('quota_id', $quota_id)
            ->select('id','mount_balance', 'mount_balance_total', 'final_balance')
            ->orderBy('id', 'asc')
            ->get();

        $lastPaymentBalanceTotal = 0;
        $lastFinalBalance = $monthlyPayment;

        foreach ($payments as $payment) {
            $payment->mount_balance_total = floatval( $lastPaymentBalanceTotal + $payment->mount_balance );
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
        }else{
            $monthlyRefundQuota = Quota::where('id',$quota_id)->first()->monthly_payment;
        }

        $refundQuotas = RefundQuota::where('quota_id', $quota_id)
            ->select('id','mount_balance', 'mount_balance_total', 'final_balance')
            ->orderBy('id', 'asc')
            ->get();

        $lastFinalBalance = $monthlyRefundQuota;

        foreach ($refundQuotas as $refundQuota) {
            $refundQuota->mount_balance_total = floatval( $lastRefundQuotaBalanceTotal - $refundQuota->mount_balance );
            $refundQuota->final_balance = floatval($lastFinalBalance - $refundQuota->mount_balance);
            $refundQuota->save();

            $lastRefundQuotaBalanceTotal = $refundQuota->mount_balance_total;
            $lastFinalBalance = $refundQuota->final_balance;
        }
    }

    public function createSpecialNegotiation($data): SpecialNegotiation|null
    {
        unset($data['_token']);
        $invoices_ids = $data['invoice_id'];
        unset($data['invoice_id']);
        $negotiation = SpecialNegotiation::create($data);
        $negotiation->invoices()->attach($invoices_ids);
        return $negotiation;
    }

    public function updateSpecialNegotiation($id, $data): SpecialNegotiation|null
    {
        $invoices_ids = $data['invoice_id'];
        unset($data['invoice_id']);
        unset($data['_token']);
        $negotiation = SpecialNegotiation::find($id);
        $negotiation->activateTracking();
        $negotiation->setReason($data['reason']);
        unset($data['reason']);
        $negotiation->update($data);
        $negotiation->invoices()->sync($invoices_ids);
        return $negotiation;
    }

    public function firstShowSpecialNegotiation($id): SpecialNegotiation|null
    {
        $negotiation = SpecialNegotiation::where('id', $id)->with([
            'invoices:id,invoice_number,amount,discount_negotiations',
            'route:id,name', 'account:id,name', 'employee:id,first_name,last_name',
            'client:id,name,company_name,phone,work_phone,address1',
            'quotas', 'quotas.invoices:id,invoice_number,amount,discount_negotiations',
            'quotas.payments', 'quotas.discounts', 'quotas.refunds'
        ])->first();
        return $negotiation;
    }

    public function createQuotas($data, $negosation_id)
    {
        foreach ($data as $value) {
            $invoices = $value['invoice_id'];
            unset($value['invoice_id']);
            $quota = Quota::create($value);
            $quota->invoices()->sync($invoices);
        }
        $quotas = Quota::where('special_negotiations_id', $negosation_id)->get();
        return $quotas;
    }

    public function updateQuota($id, $data)
    {
        $quota = Quota::find($id);
        if (!isset($quota)) {
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
        return $payment;
    }

    public function updatePayment($id, $data)
    {
        $payment = PaymentQuota::find($id);
        if (!isset($payment)) {
            return false;
        }
        unset($data['_token']);
        $payment->activateTracking();
        $payment->setReason($data['reason']);
        unset($data['reason']);
        $payment->update($data);

        $quota_id = $payment->quota_id;
        $this->paymentCalculate($quota_id);
        return $payment;
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
        if(isset($paymentCuota)){
            $monthlyRefund = $paymentCuota->final_balance;
        }

        $data['mount_balance_total'] = 0;
        $data['final_balance'] = 0;
        $data['overdue_balance'] = $is_overdue ? $monthlyRefund : 0;

        $refund = RefundQuota::create($data);
        $this->refundCalculate($quota_id);
        return $refund;
    }

    public function updateRefund($id, $data)
    {
        $refund = RefundQuota::find($id);

        if (!isset($refund)) {
            return false;
        }
        unset($data['_token']);
        $refund->activateTracking();
        $refund->setReason($data['reason']);
        unset($data['reason']);
        $refund->update($data);

        $quota_id = $refund->quota_id;
        $this->refundCalculate($quota_id);
        return $refund;
    }

    public function createDiscount($data)
    {
        unset($data['_token']);
        $quota_id = $data['quota_id'];
        $final_balance = $this->calculateFinalBalance($quota_id);
        $data['final_balance'] = $final_balance - $data['discount_applied'];
        $discount = DiscountQuota::create($data);
        $this->insertDiscountInvoice($discount);
        return true;
    }

    public function updateDiscount($id, $data)
    {
        $discount = DiscountQuota::find($id);
        if (!isset($discount)) {
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
        return $discount;
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
    }

    public function clearDiscountInvoicetoUpdate($discount)
    {
        $discount_negotiation = $discount->discount_applied;
        $invoice = $discount->invoice;

        $invoice->discount_negotiations -= $discount_negotiation;
        $invoice->discount -= $discount_negotiation;
        $invoice->amount += $discount_negotiation;
        $invoice->replacement_amount += $discount_negotiation;
        $invoice->save();
    }

    public function setCreditRecord($id, $data)
    {
        $negotiation = SpecialNegotiation::find($id);
        if (!isset($negotiation)) {
            return false;
        }
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
}
