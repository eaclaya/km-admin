<?php
namespace App\Services;


use App\Models\FinanceCatalogueItem;
use App\Models\FinanceDaybookEntry;
use App\Models\Main\Account;
use App\Models\Main\Invoice;
use App\Models\Main\Payment;
use App\Repositories\DaybookRepository;

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
            $this->processInvoices($currentStores, $date);
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
//        chequear el metodo del pago para chocar el contra de la partida
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
                    'partial' => $payment->total_paid,
                    'debit' => $payment->total_paid,
                    'havings' => $payment->total_paid,
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
                        'debit' => $payment->total_paid,
                        'havings' => 0,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                        'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->total_paid,
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
                        'partial' => $payment->total_paid,
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
                        'havings' => $payment->total_paid,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                        'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->total_paid,
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
                'partial' => $payment->total_paid,
                'debit' => $payment->total_paid,
                'havings' => $payment->total_paid,
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
                    'debit' => $payment->total_paid,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                    'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->total_paid,
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
                    'partial' => $payment->total_paid,
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
                    'havings' => $payment->total_paid,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                    'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->total_paid,
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
            ->whereIn('account_id', $currentStores)
            ->whereDate('created_at', $date)
            ->where('invoice_type_id', INVOICE_TYPE_STANDARD)
            ->with(['payments'])
            ->get();
//        buscar factura para las de credito y buscar los pagos para las de contado
//        en los pagos tengo que saber si son a credito, si son a credito tiene otra estructura
//        chequear el metodo del pago para chocar el contra de la partida
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
                    'partial' => $payment->total_paid,
                    'debit' => $payment->total_paid,
                    'havings' => $payment->total_paid,
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
                        'debit' => $payment->total_paid,
                        'havings' => 0,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                        'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->total_paid,
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
                        'partial' => $payment->total_paid,
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
                        'havings' => $payment->total_paid,
                    ],
                    [
                        'account_id' => $invoice->account_id,
                        'organization_company_id' => $accounts[$invoice->account_id],
                        'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                        'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => $payment->total_paid,
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
                'partial' => $payment->total_paid,
                'debit' => $payment->total_paid,
                'havings' => $payment->total_paid,
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
                    'debit' => $payment->total_paid,
                    'havings' => 0,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $cajaCompanyId[$accounts[$invoice->account_id]]->finance_account_name,
                    'finance_catalogue_item_id' => $cajaCompanyId[$accounts[$invoice->account_id]]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->total_paid,
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
                    'partial' => $payment->total_paid,
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
                    'havings' => $payment->total_paid,
                ],
                [
                    'account_id' => $invoice->account_id,
                    'organization_company_id' => $accounts[$invoice->account_id],
                    'description' => $ingresoAccountId[$invoice->account_id]->finance_account_name,
                    'finance_catalogue_item_id' => $ingresoAccountId[$invoice->account_id]->id,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                    'partial' => $payment->total_paid,
                    'debit' => 0,
                    'havings' => 0,
                ],
            ];
            $entry = new FinanceDaybookEntry();
            $entry->createNew($input, $items);
        }*/;
    }
}
