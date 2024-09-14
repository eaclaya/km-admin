<?php
namespace App\Services;


use App\Models\FinanceCatalogueItem;
use App\Models\FinanceDaybookEntry;
use App\Models\Main\Account;
use App\Models\Main\Invoice;
use App\Models\Main\Payment;
use App\Repositories\DaybookRepository;
use \DB;
use \Carbon\Carbon;

class DaybookService
{
    protected DaybookRepository $daybookRepository;
    public function __construct(DaybookRepository $daybookRepository)
    {
        $this->daybookRepository = $daybookRepository;
    }

    public function getRepo()
    {
        return $this->daybookRepository;
    }

    public function initProcess($type, $currentStores, $date)
    {
        if ($type === 'invoices') {
            return true;
            // $this->processInvoices($currentStores, $date);
        }
        if ($type === 'payments') {
            $this->processPayments($currentStores, $date);
        }
    }

    public function processInvoices($currentStores, $date)
    {
        $invoices = Invoice::query()
            ->whereIn('account_id', $currentStores)
            ->whereDate('created_at', $date)
            ->where('invoice_type_id', INVOICE_TYPE_STANDARD)
            ->with(['payments'])
            ->get();
        //chequear el metodo del pago para chocar el contra de la partida
        //buscar factura para las de credito y buscar los pagos para las de contado
        //en los pagos tengo que saber si son a credito, si son a credito tiene otra estructura
        //chequear el metodo del pago para chocar el contra de la partida
        [$creditInvoices, $nonCreditInvoices] = $invoices->partition(function ($invoice) {
            return $invoice->is_credit;
        });
        $accounts = Account::whereIn('id',$invoices->pluck('account_id')->toArray())->pluck('organization_company_id','id')->toArray();

        $cajaId = FinanceCatalogueItem::where('finance_account_name', 'Caja')->first()->id;
        $cajaCompanyId = FinanceCatalogueItem::where('sub_item_id', $cajaId)
                            ->get()->keyBy('model_id');
        $cajaAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $cajaCompanyId->pluck('id'))
                            ->get()->keyBy('model_id');

        $ingresoId = FinanceCatalogueItem::where('finance_account_name', 'TOTAL INGRESOS')->first()->id;
        $ingresoCompanyId = FinanceCatalogueItem::where('sub_item_id', $ingresoId)
                            ->get()->keyBy('model_id');
        $ingresoAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $ingresoCompanyId->pluck('id'))
                            ->get()->keyBy('model_id');

        foreach($nonCreditInvoices as $invoice){
            $payments = $invoice->payments;
            foreach ($payments as $payment){
                $entry = [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => 'Ventas por Factura',
                    'user_id' => $invoice->user_id,
                    'real_user_id' => $invoice->real_user_id,
                    'partial' => $payment->amount,
                    'debit' => $payment->amount,
                    'havings' => $payment->amount,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                ];
                $items = [
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => 'Caja',
                        'finance_catalogue_item_id' => $cajaId,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => $payment->amount,
                        'havings' => 0,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                        'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->amount,
                        'debit' => 0,
                        'havings' => 0,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $cajaAccountId[$invoice->account_id]->finance_account_name,
                        'finance_catalogue_item_id' => $cajaAccountId[$invoice->account_id]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->amount,
                        'debit' => 0,
                        'havings' => 0,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $ingresoCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                        'finance_catalogue_item_id' => $ingresoCompanyId[$accounts[$invoice->account_id]]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => 0,
                        'havings' => $payment->amount,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                        'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->amount,
                        'debit' => 0,
                        'havings' => 0,
                    ],
                ];
                $this->daybookRepository->createNew($entry, $items);
            }
        };

        /*$cuentasId = FinanceCatalogueItem::where('finance_account_name', 'cuentas por cobrar')->first()->id;
        $clientesId = FinanceCatalogueItem::where('sub_item_id', $cuentasId)->where('finance_account_name','clientes')
            ->first()->id;

        $ingresoId = FinanceCatalogueItem::where('finance_account_name', 'TOTAL INGRESOS')->first()->id;
        $ingresoCompanyId = FinanceCatalogueItem::where('sub_item_id', $ingresoId)
            ->get()->keyBy('model_id');
        $ingresoAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $ingresoCompanyId->pluck('id'))
            ->get()->keyBy('model_id');

        foreach($creditInvoices as $invoice) {
            $input = [
                'account_id' => $invoice->account_id,
                'organization_company_id' => $accounts[$invoice->account_id],
                'description' => 'Ventas por Factura',
                'user_id' => $invoice->user_id,
                'real_user_id' => $invoice->real_user_id,
                'partial' => $payment->amount,
                'debit' => $payment->amount,
                'havings' => $payment->amount,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
                'model' => ENTITY_PAYMENT,
                'model_id' => $payment->id,
            ];
            $items = [
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => 'Caja',
                    'finance_catalogue_item_id' => $cajaId,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => 0,
                    'debit' => $payment->amount,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                    'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $cajaAccountId[$invoice->account_id]->finance_account_name,
                    'finance_catalogue_item_id' => $cajaAccountId[$invoice->account_id]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $ingresoCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                    'finance_catalogue_item_id' => $ingresoCompanyId[$accounts[$invoice->account_id]]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => 0,
                    'debit' => 0,
                    'havings' => $payment->amount,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                    'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
            ];
            $entry = new FinanceDaybookEntry();
            $entry->createNew($input, $items);
        }*/;
    }

    public function processPayments($currentStores, $date)
    {
        $payments = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->whereIn('payments.account_id', $currentStores)
            ->whereDate('payments.created_at', $date)
            ->where('invoices.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('payments.payment_type_id', 1)
            ->where('payments.payment_status_id', '>', 3)
            ->where('invoices.is_credit', 0)
            ->select(
                'payments.id',
                'invoices.account_id',
                'invoices.user_id',
                'invoices.real_user_id',
                'payments.amount',
                'created_at',
                'updated_at'
            )
            ->get();

        $accounts = Account::whereIn('id',$payments->pluck('account_id')->toArray())->pluck('organization_company_id','id')->toArray();

        $cajaId = FinanceCatalogueItem::where('finance_account_name', 'Caja')->first()->id;
        $cajaCompanyId = FinanceCatalogueItem::where('sub_item_id', $cajaId)
            ->get()->keyBy('model_id');
        $cajaAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $cajaCompanyId->pluck('id'))
            ->get()->keyBy('model_id');

        $ingresoId = FinanceCatalogueItem::where('finance_account_name', 'TOTAL INGRESOS')->first()->id;
        $ingresoCompanyId = FinanceCatalogueItem::where('sub_item_id', $ingresoId)
            ->get()->keyBy('model_id');
        $ingresoAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $ingresoCompanyId->pluck('id'))
            ->get()->keyBy('model_id');


        foreach ($payments as $payment){
            $account_id = isset($payment->account_id) ? $payment->account_id : 0;
            $company_id = isset($accounts[$account_id]) ? $accounts[$account_id] : 0;
            $created_at = (!$payment->created_at->isValid() || $payment->created_at->year <= 0) ? Carbon::createFromFormat('Y-m-d H:i:s', '1970-01-01 00:00:00')->format('Y-m-d H:i:s') : $payment->created_at->toDateTimeString();
            $updated_at = (!$payment->updated_at->isValid() || $payment->updated_at->year <= 0) ? $created_at : $payment->updated_at->toDateTimeString();
            $entry = [
                'account_id' => $account_id,
                'organization_company_id' => $company_id,
                'description' => 'Ventas por Factura a Credito',
                'user_id' => $payment->user_id,
                'real_user_id' => $payment->real_user_id,
                'partial' => $payment->amount,
                'debit' => $payment->amount,
                'havings' => $payment->amount,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'model' => ENTITY_PAYMENT,
                'model_id' => $payment->id,
            ];
            $items = [
                [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => 'Caja',
                    'finance_catalogue_item_id' => $cajaId,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => 0,
                    'debit' => $payment->amount,
                    'havings' => 0,
                ],
                [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => isset($cajaCompanyId[$company_id]) ? $cajaCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                    'finance_catalogue_item_id' => isset($cajaCompanyId[$company_id]) ? $cajaCompanyId[$company_id]->id : 0,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
                [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => isset($cajaAccountId[$account_id]) ? $cajaAccountId[$account_id]->finance_account_name : 'error en catalogo',
                    'finance_catalogue_item_id' => isset($cajaAccountId[$account_id]) ? $cajaAccountId[$account_id]->id : 0,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
                [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => isset($ingresoCompanyId[$company_id]) ? $ingresoCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                    'finance_catalogue_item_id' => isset($ingresoCompanyId[$company_id]) ? $ingresoCompanyId[$company_id]->id : 0,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => 0,
                    'debit' => 0,
                    'havings' => $payment->amount,
                ],
                [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => isset($ingresoAccountId[$account_id]) ? $ingresoAccountId[$account_id]->finance_account_name : 'error en catalogo',
                    'finance_catalogue_item_id' => isset($ingresoAccountId[$account_id]) ? $ingresoAccountId[$account_id]->id : 0,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
            ];
            $this->daybookRepository->createNew($entry, $items);
        };

        /*$cuentasId = FinanceCatalogueItem::where('finance_account_name', 'cuentas por cobrar')->first()->id;
        $clientesId = FinanceCatalogueItem::where('sub_item_id', $cuentasId)->where('finance_account_name','clientes')
            ->first()->id;

        $ingresoId = FinanceCatalogueItem::where('finance_account_name', 'TOTAL INGRESOS')->first()->id;
        $ingresoCompanyId = FinanceCatalogueItem::where('sub_item_id', $ingresoId)
            ->get()->keyBy('model_id');
        $ingresoAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $ingresoCompanyId->pluck('id'))
            ->get()->keyBy('model_id');

        foreach($creditInvoices as $invoice) {
            $input = [
                'account_id' => $invoice->account_id,
                'organization_company_id' => $accounts[$invoice->account_id],
                'description' => 'Ventas por Factura',
                'user_id' => $invoice->user_id,
                'real_user_id' => $invoice->real_user_id,
                'partial' => $payment->amount,
                'debit' => $payment->amount,
                'havings' => $payment->amount,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
                'model' => ENTITY_PAYMENT,
                'model_id' => $payment->id,
            ];
            $items = [
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => 'Caja',
                    'finance_catalogue_item_id' => $cajaId,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => 0,
                    'debit' => $payment->amount,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                    'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $cajaAccountId[$invoice->account_id]->finance_account_name,
                    'finance_catalogue_item_id' => $cajaAccountId[$invoice->account_id]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $ingresoCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                    'finance_catalogue_item_id' => $ingresoCompanyId[$accounts[$invoice->account_id]]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => 0,
                    'debit' => 0,
                    'havings' => $payment->amount,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                    'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->amount,
                    'debit' => 0,
                    'havings' => 0,
                ],
            ];
            $entry = new FinanceDaybookEntry();
            $entry->createNew($input, $items);
        }*/;
    }
}
