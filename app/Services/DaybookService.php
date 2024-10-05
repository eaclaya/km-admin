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
            $this->processInvoices($currentStores, $date);
            $this->processPayments($currentStores, $date);
        }
        if ($type === 'payments') {
            return true;
        }
    }

    public function processInvoices($currentStores, $date)
    {
        return true;
        $invoices = Invoice::query()
            ->whereIn('account_id', $currentStores)
            ->whereDate('created_at', $date)
            ->where('invoice_type_id', INVOICE_TYPE_STANDARD)
            ->with(['payments'])
            ->get();
        //chequear el metodo del pago para chocar el contra de la partida
        //buscar factura para las de credito
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
        // Ventas a Debito en Efectivo
        $payments = Payment::query()
            ->leftJoin('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->leftJoin('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->leftJoin('finance_banks_accounts', 'payment_types.finances_bank_account_id', '=', 'finance_banks_accounts.id')
            ->whereIn('payments.account_id', $currentStores)
            ->whereDate('payments.created_at', $date)
            ->where('invoices.invoice_type_id', INVOICE_TYPE_STANDARD)
            ->whereNotIn('payment_types.payment_class_id', [2,6])
            ->where('payments.payment_status_id', '>', 3)
            ->where('invoices.is_credit', 0)
            ->select(
                'payments.id',
                'payments.payment_type_id',
                'payments.invoice_id',
                'invoices.account_id',
                'invoices.user_id',
                'invoices.real_user_id',

                'invoices.discount_percent',
                'invoices.discount_points',
                'invoices.discount_vouchers',
                'invoices.discount',
                'invoices.amount',
                'invoices.total_cost',

                'payments.amount',
                'payments.balance',
                'payments.created_at',
                'payments.updated_at',
                'payment_types.payment_class_id',
                'payment_types.finances_bank_account_id as bank_account_id',
                'finance_banks_accounts.bank_id'
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

        $costoId = FinanceCatalogueItem::where('finance_account_name', 'COSTO')->first()->id;
        $costoCompanyId = FinanceCatalogueItem::where('sub_item_id', $costoId)
            ->get()->keyBy('model_id');
        $costoAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $costoCompanyId->pluck('id'))
            ->get()->keyBy('model_id');

        $almacenId = FinanceCatalogueItem::where('finance_account_name', 'Almacen')->first()->id;
        $almacenCompanyId = FinanceCatalogueItem::where('sub_item_id', $almacenId)
            ->get()->keyBy('model_id');
        $almacenAccountId = FinanceCatalogueItem::whereIn('sub_item_id', $almacenCompanyId->pluck('id'))
            ->get()->keyBy('model_id');

        $bankId = FinanceCatalogueItem::where('finance_account_name', 'Bancos')->first()->id;
        $banksId = FinanceCatalogueItem::where('sub_item_id', $bankId)
            ->get()->keyBy('model_id');
        $accountsBanksId = FinanceCatalogueItem::whereIn('sub_item_id', $banksId->pluck('id'))
            ->get()->keyBy('model_id');

        foreach ($payments as $payment){

            $entry = [];
            $items = [];

            $account_id = isset($payment->account_id) ? $payment->account_id : 0;
            $company_id = isset($accounts[$account_id]) ? $accounts[$account_id] : 0;

            $created_at = (!$payment->created_at->isValid() || $payment->created_at->year <= 0) ? Carbon::createFromFormat('Y-m-d H:i:s', '1970-01-01 00:00:00')->format('Y-m-d H:i:s') : $payment->created_at->toDateTimeString();
            $updated_at = (!$payment->updated_at->isValid() || $payment->updated_at->year <= 0) ? $created_at : $payment->updated_at->toDateTimeString();

            if($payment->payment_class_id == 1){
                $entry = [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => 'Ventas al Contado en Efectivo',
                    'user_id' => $payment->user_id,
                    'real_user_id' => $payment->real_user_id,
                    'partial' => 0,
                    'debit' => $payment->amount,
                    'havings' => 0,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                ];
                $items = [
                    // Caja
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
                        'is_primary' => 1
                    ],
                    // Caja empresa
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => isset($cajaCompanyId[$company_id]) ? $cajaCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                        'finance_catalogue_item_id' => isset($cajaCompanyId[$company_id]) ? $cajaCompanyId[$company_id]->id : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => $payment->amount,
                        'havings' => 0,
                        'is_primary' => 1
                    ],
                    // Caja tienda
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => isset($cajaAccountId[$account_id]) ? $cajaAccountId[$account_id]->finance_account_name : 'error en catalogo',
                        'finance_catalogue_item_id' => isset($cajaAccountId[$account_id]) ? $cajaAccountId[$account_id]->id : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => $payment->amount,
                        'havings' => 0,
                        'is_primary' => 1
                    ],
                    // Ingreso
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => 'Total de Ingresos',
                        'finance_catalogue_item_id' => isset($ingresoId) ? $ingresoId : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => 0,
                        'havings' => $payment->amount,
                        'is_primary' => 2
                    ],
                    // Ingresos Empresa
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
                        'is_primary' => 2
                    ],
                    // Ingresos tienda
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => isset($ingresoAccountId[$account_id]) ? $ingresoAccountId[$account_id]->finance_account_name : 'error en catalogo',
                        'finance_catalogue_item_id' => isset($ingresoAccountId[$account_id]) ? $ingresoAccountId[$account_id]->id : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => 0,
                        'havings' => $payment->amount,
                        'is_primary' => 2
                    ],
                ];
                if($payment->isFinish()){
                    $items = array_merge($items, [
                        // Costo de Venta
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => 'Costo',
                            'finance_catalogue_item_id' => $costoId,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => $payment->total_cost,
                            'havings' => 0,
                            'is_primary' => 3
                        ],
                        // Costo Empresa
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($costoCompanyId[$company_id]) ? $costoCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($costoCompanyId[$company_id]) ? $costoCompanyId[$company_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => $payment->total_cost,
                            'havings' => 0,
                            'is_primary' => 3
                        ],
                        // Costo tienda
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($costoAccountId[$account_id]) ? $costoAccountId[$account_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($costoAccountId[$account_id]) ? $costoAccountId[$account_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => $payment->total_cost,
                            'havings' => 0,
                            'is_primary' => 3
                        ],

                        // Almacen
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => 'Almacen',
                            'finance_catalogue_item_id' => isset($almacenId) ? $almacenId : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => 0,
                            'havings' => $payment->total_cost,
                            'is_primary' => 4
                        ],
                        // Almacen Empresa
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($almacenCompanyId[$company_id]) ? $almacenCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($almacenCompanyId[$company_id]) ? $almacenCompanyId[$company_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => 0,
                            'havings' => $payment->total_cost,
                            'is_primary' => 4
                        ],
                        // Almacen tienda
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($almacenAccountId[$account_id]) ? $almacenAccountId[$account_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($almacenAccountId[$account_id]) ? $almacenAccountId[$account_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => 0,
                            'havings' => $payment->total_cost,
                            'is_primary' => 4
                        ],
                        //------ aqui viene el inventario
                    ]);
                }
            }elseif(in_array($payment->payment_class_id, [3,4,5,7,8])) {
                $description = 'Ventas al Contado en ';

                switch ($payment->payment_class_id) {
                    case 3:
                        $description .= 'POS';
                        break;
                    case 4:
                        $description .= 'Banco';
                        break;
                    case 5:
                        $description .= 'Tigo';
                        break;
                    case 7:
                        $description .= 'SmartLink';
                        break;
                    case 8:
                        $description .= 'PixelPay';
                        break;
                }

                $entry = [
                    'account_id' => $account_id,
                    'organization_company_id' => $company_id,
                    'description' => $description,
                    'user_id' => $payment->user_id,
                    'real_user_id' => $payment->real_user_id,
                    'partial' => 0,
                    'debit' => $payment->amount,
                    'havings' => 0,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                    'model' => ENTITY_PAYMENT,
                    'model_id' => $payment->id,
                ];
                $items = [
                    // Banco
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => 'Banco',
                        'finance_catalogue_item_id' => $bankId,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => $payment->amount,
                        'havings' => 0,
                        'is_primary' => 1
                    ],
                    // Banco Associado
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => isset($banksId[$payment->bank_id]) ? $banksId[$payment->bank_id]->finance_account_name : 'error en catalogo',
                        'finance_catalogue_item_id' => isset($banksId[$payment->bank_id]) ? $banksId[$payment->bank_id]->id : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => $payment->amount,
                        'havings' => 0,
                        'is_primary' => 1
                    ],
                    // Cuenta Bancaria Associada
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => isset($accountsBanksId[$payment->bank_account_id]) ? $accountsBanksId[$payment->bank_account_id]->finance_account_name : 'error en catalogo',
                        'finance_catalogue_item_id' => isset($accountsBanksId[$payment->bank_account_id]) ? $accountsBanksId[$payment->bank_account_id]->id : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => $payment->amount,
                        'havings' => 0,
                        'is_primary' => 1
                    ],
                    // Ingreso
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => 'Total de Ingresos',
                        'finance_catalogue_item_id' => isset($ingresoId) ? $ingresoId : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => 0,
                        'havings' => $payment->amount,
                        'is_primary' => 2
                    ],
                    // Ingresos Empresa
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
                        'is_primary' => 2
                    ],
                    // Ingresos tienda
                    [
                        'account_id' => $account_id,
                        'organization_company_id' => $company_id,
                        'description' => isset($ingresoAccountId[$account_id]) ? $ingresoAccountId[$account_id]->finance_account_name : 'error en catalogo',
                        'finance_catalogue_item_id' => isset($ingresoAccountId[$account_id]) ? $ingresoAccountId[$account_id]->id : 0,
                        'model' => ENTITY_PAYMENT,
                        'model_id' => $payment->id,
                        'partial' => 0,
                        'debit' => 0,
                        'havings' => $payment->amount,
                        'is_primary' => 2
                    ],
                ];
                if($payment->isFinish()){
                    $items = array_merge($items, [
                        // Costo de Venta
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => 'Costo',
                            'finance_catalogue_item_id' => $costoId,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => $productCostTotal,
                            'havings' => 0,
                            'is_primary' => 3
                        ],
                        // Costo Empresa
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($costoCompanyId[$company_id]) ? $costoCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($costoCompanyId[$company_id]) ? $costoCompanyId[$company_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => $productCostTotal,
                            'havings' => 0,
                            'is_primary' => 3
                        ],
                        // Costo tienda
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($costoAccountId[$account_id]) ? $costoAccountId[$account_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($costoAccountId[$account_id]) ? $costoAccountId[$account_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => $productCostTotal,
                            'havings' => 0,
                            'is_primary' => 3
                        ],

                        // Almacen
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => 'Almacen',
                            'finance_catalogue_item_id' => isset($almacenId) ? $almacenId : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => 0,
                            'havings' => $productCostTotal,
                            'is_primary' => 4
                        ],
                        // Almacen Empresa
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($almacenCompanyId[$company_id]) ? $almacenCompanyId[$company_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($almacenCompanyId[$company_id]) ? $almacenCompanyId[$company_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => 0,
                            'havings' => $productCostTotal,
                            'is_primary' => 4
                        ],
                        // Almacen tienda
                        [
                            'account_id' => $account_id,
                            'organization_company_id' => $company_id,
                            'description' => isset($almacenAccountId[$account_id]) ? $almacenAccountId[$account_id]->finance_account_name : 'error en catalogo',
                            'finance_catalogue_item_id' => isset($almacenAccountId[$account_id]) ? $almacenAccountId[$account_id]->id : 0,
                            'model' => ENTITY_PAYMENT,
                            'model_id' => $payment->id,
                            'partial' => 0,
                            'debit' => 0,
                            'havings' => $productCostTotal,
                            'is_primary' => 4
                        ],
                        //------ aqui viene el inventario
                    ]);
                }
            }
            $this->daybookRepository->createNew($entry, $items);
        };

    }
}
