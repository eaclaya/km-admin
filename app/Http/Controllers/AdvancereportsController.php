<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Cache;
use URL;
use View;
use Utils;
use Input;
use Session;
use Redirect;
use Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Jobs\ReportStockByVendor;
use App\Jobs\ReportQuotedProformProducts;
use App\Jobs\ReportSalesBySeller;
use App\Jobs\ReportInputEntries;
use App\Jobs\ReportExportClients;
use App\Jobs\ReportExportInvoices;
use App\Jobs\ReportCommissionOldProducts;
use App\Jobs\ReportStockInStores;
use App\Models\ReportProcess;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AdvancereportsController extends Controller
{
    /* public function __construct()
    {
        define("CSV_SEPARATOR", ["Sep=;"]);
    } */

    public function index()
    {
        return view('advancereports.index');
    }
    public function tutorials()
    {
        return view('advancereports.tutorials');
    }

    public function customersInactive(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $value = $data['value'] > 0 ? $data['value'] : 0;
            $type = isset($data['type']) ? $data['type'] : 'day';
            $result = [];
            $offset = 0;
            if ($type == 'day') {
                $offset = $value;
            } elseif ($type == 'month') {
                $offset = $value * 30;
            } else {
                $offset = $value * 365;
            }
            $date = new \DateTime();
            $to_date = $date->modify("-$offset day")->format('Y-m-d');
            $last_year = $date->modify('-2 year')->format('Y-m-d');
            $invoices = DB::table('clients')->join('invoices', 'clients.id', '=', 'invoices.client_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('clients.id', 'clients.company_name', 'clients.name', 'clients.seller_id', 'clients.work_phone', 'clients.phone', 'clients.points', 'accounts.name as account', 'invoices.invoice_date', 'invoices.id as invoices')
                ->whereDate('invoices.invoice_date', '>=', $last_year)
                ->where('accounts.exclude', 0)
                ->get();
            $clients = [];
            foreach ($invoices as $invoice) {
                if (isset($clients[$invoice->id]) == false) {
                    $clients[$invoice->id] = $invoice;
                    $clients[$invoice->id]->invoices = 0;
                } else {
                    if (strtotime($invoice->invoice_date) > strtotime($clients[$invoice->id]->invoice_date)) {
                        $clients[$invoice->id]->invoice_date = $invoice->invoice_date;
                    }
                }
                $clients[$invoice->id]->invoices += 1;
            }
            $employees = Employee::get()->keyBy('id');
            foreach ($clients as $client) {
                if (strtotime($client->invoice_date) > strtotime($to_date)) {
                    continue;
                }
                $result[$client->id] = [
                    'id' => $client->id,
                    'name' => $client->company_name,
                    'firstname' => $client->name,
                    'phone' =>  $client->phone ? $client->phone : $client->work_phone,
                    'points' => $client->points,
                    'account' => $client->account,
                    'employee' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id]->first_name . " " . $employees[$client->seller_id]->last_name : '',
                    'profile' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id]->profile : '',
                    'invoicesQtyTotal' => $client->invoices,
                    'lastPurchaseDate' => $client->invoice_date
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('customers_inactive', $result);
        }
        return view('advancereports.customers_inactive', ['result' => $result]);
    }

    public function clientsByDate(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $clients = Client::with('account')->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<', $to_date)->with('account')->get();
            $employees = Employee::get()->keyBy('id');
            $result = [];
            foreach ($clients as $client) {
                $result[] = [
                    'name' => $client->company_name ? $client->company_name : $client->name,
                    'phone' => $client->work_phone ? $client->work_phone : $client->phone,
                    'account' => $client->account->name,
                    'employee' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id]->first_name . " " . $employees[$client->seller_id]->last_name : '',
                    'created_at' => $client->created_at
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('clients_by_date', $result);
        }
        return view('advancereports.clients_by_date', ['result' => $result]);
    }

    public function salesByClientType(Request $request)
    {
        $startDate = $request->from_date;
        $endDate = $request->to_date;
        $isExport = $request->export;
        $accounts = Account::select('id', 'name')->get();
        if ($isExport && $startDate && $endDate) {
            $columns = ['Tipo', 'Tienda', 'Total', 'Balance', 'Pagado'];
            $items = DB::table('invoices')->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->select(DB::raw('SUM(invoices.amount) as amount'), DB::raw('SUM(invoices.balance) as balance'), 'clients.type', 'accounts.name')
                ->whereDate('invoices.invoice_date', '>=', $startDate)->whereDate('invoices.invoice_date', '<', $endDate)
                ->where('invoices.invoice_type_id', 1)->where('accounts.exclude', 0);
            if ($request->account_id) {
                $items->where('invoices.account_id', $request->account_id);
            }
            $items = $items->groupBy('clients.type')->groupBy('invoices.account_id')->orderBy('invoices.account_id')->get();
            $fp = fopen('reporte.csv', 'w');
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($items as $item) {
                $displayData = [
                    $item->type,
                    $item->name ? $item->name : 'N/A',
                    $item->amount,
                    $item->balance,
                    $item->amount - $item->balance
                ];
                fputcsv($fp, $displayData, ';');
            }
            fclose($fp);
            return redirect('/reporte.csv');
        }
        return view('advancereports.sales_by_client_type', ['accounts' => $accounts]);
    }

    public function salesByCategory(Request $request)
    {
        $data = $request->all();
        $stores = Account::select('id', 'name')->get();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $invoices = [];
            if ($store) {
                $invoices = DB::table('invoices')->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->join('products', 'products.id', '=', 'invoice_items.product_id')
                    ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                    ->join('categories', 'categories.category_id', '=', 'products.category_id')
                    ->select('invoice_items.qty', 'invoice_items.cost', 'categories.name', 'products.category_id')
                    ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                    ->where('invoices.invoice_type_id', 1)->whereDate('invoices.created_at', '>=', $from_date)
                    ->whereDate('invoices.created_at', '<=', $to_date)->where('invoices.account_id', $store)->get();
            } else {
                $invoices = DB::table('invoices')->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->join('products', 'products.id', '=', 'invoice_items.product_id')
                    ->join('categories', 'categories.category_id', '=', 'products.category_id')
                    ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                    ->select('invoice_items.qty', 'invoice_items.cost', 'categories.name', 'products.category_id')
                    ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                    ->where('invoices.invoice_type_id', 1)->whereDate('invoices.created_at', '>=', $from_date)
                    ->whereDate('invoices.created_at', '<=', $to_date)->get();
            }
            $result = [];
            foreach ($invoices as $item) {
                if (isset($result[$item->category_id]) == false) {
                    $result[$item->category_id] = [
                        'qty' => 0,
                        'total' => 0,
                        'category' => $item->name
                    ];
                }
                $result[$item->category_id]['qty'] += intval($item->qty);
                $result[$item->category_id]['total'] += (intval($item->qty) * floatval($item->cost));
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_category', $result);
        }
        return view('advancereports.sales_by_category', ['result' => $result, 'stores' => $stores]);
    }

    public function expensesByCategories(Request $request)
    {
        $data = $request->all();
        $stores = Account::select('id', 'name')->get();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $expenses = [];
            if ($store) {
                $expenses = DB::table('expenses')->join('accounts', 'expenses.account_id', '=', 'accounts.id')
                    ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
                    ->join('expense_subcategories', 'expense_subcategories.id', '=', 'expenses.expense_subcategory_id')
                    ->selectRaw('expenses.expense_date, accounts.name as account_name, expense_categories.name as category_name, expense_subcategories.name as subcategory_name, sum(expenses.amount) as amount')
                    ->whereDate('expenses.expense_date', '>=', $from_date)->whereDate('expenses.expense_date', '<', $to_date)
                    ->where('accounts.id', $store)
                    ->groupBy('expenses.expense_date')
                    ->groupBy('accounts.id')
                    ->groupBy('expense_categories.id')
                    ->groupBy('expense_subcategories.id')
                    ->get();
            } else {
                $expenses = DB::table('expenses')->join('accounts', 'expenses.account_id', '=', 'accounts.id')
                    ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
                    ->join('expense_subcategories', 'expense_subcategories.id', '=', 'expenses.expense_subcategory_id')
                    ->selectRaw('expenses.expense_date, accounts.name as account_name, expense_categories.name as category_name, expense_subcategories.name as subcategory_name, sum(expenses.amount) as amount')
                    ->whereDate('expenses.expense_date', '>=', $from_date)
                    ->whereDate('expenses.expense_date', '<', $to_date)
                    ->groupBy('expenses.expense_date')
                    ->groupBy('accounts.id')
                    ->groupBy('expense_categories.id')
                    ->groupBy('expense_subcategories.id')
                    ->get();
            }
            $result = [];
            foreach ($expenses as $item) {
                $result[] = [
                    'expense_date' => $item->expense_date,
                    'account_name' => $item->account_name,
                    'category_name' => $item->category_name,
                    'subcategory_name' => $item->subcategory_name,
                    'amount' => $item->amount
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('expenses_by_category', $result);
        }
        return view('advancereports.expenses_by_category', ['result' => $result, 'stores' => $stores]);
    }

    public function receivablesByRoute(Request $request)
    {
        $data = $request->all();
        $routes = ['Ruta #1', 'Ruta #2', 'Ruta #3', 'Ruta #4', 'Ruta #5', 'Ruta #6', 'Ruta #7'];
        $clients = [];
        $invoices = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $route_name = isset($data['route_name']) ? $data['route_name'] : null;
            $invoices = [];
            if ($route_name) {
                $invoices = DB::table('invoices')
                    ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                    ->join('clients', 'clients.id', '=', 'invoices.client_id')
                    ->select('invoices.invoice_number', 'invoices.invoice_date', 'invoices.end_date', 'invoices.amount', 'invoices.balance', 'clients.route_name', 'clients.name', 'invoices.client_id', 'invoices.credit_days')
                    ->where('accounts.exclude', 0)->where('invoices.balance', '>', 0)
                    ->where('invoices.invoice_type_id', 1)->whereDate('invoices.invoice_date', '>=', $from_date)
                    ->whereDate('invoices.invoice_date', '<=', $to_date)->where('clients.route_name', $route_name)->get();
            } else {
                $invoices = DB::table('invoices')
                    ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                    ->join('clients', 'clients.id', '=', 'invoices.client_id')
                    ->select('invoices.invoice_number', 'invoices.invoice_date', 'invoices.end_date', 'invoices.amount', 'invoices.balance', 'clients.route_name', 'clients.name', 'invoices.client_id', 'invoices.credit_days')
                    ->where('accounts.exclude', 0)->where('invoices.balance', '>', 0)
                    ->whereNotNull('clients.route_name')->where('clients.route_name', '<>', 'Ninguna')
                    ->where('invoices.invoice_type_id', 1)->whereDate('invoices.invoice_date', '>=', $from_date)
                    ->whereDate('invoices.invoice_date', '<=', $to_date)->get();
            }
            $balance = 0;
            foreach ($invoices as &$item) {
                $hasPendingInvoices = true;
                $datediff = intval((time() - strtotime($item->invoice_date)) / (60 * 60 * 24));
                $datediff2 = intval((strtotime($item->end_date) - time()) / (60 * 60 * 24)) + 1;
                $datediff3 = intval((time() - intval(strtotime($item->end_date))) / (60 * 60 * 24));
                $item->datediff = $datediff < 0 ? 0 : $datediff;
                $item->datediff2 = $datediff2 < 0 ? 0 : $datediff2;
                $item->datediff3 = $datediff3 < 0 ? 0 : $datediff3;
                if (isset($clients[$item->client_id]) == false) {
                    $clients[$item->client_id] = [];
                    $clients[$item->client_id]['balance'] = 0;
                    $clients[$item->client_id]['name'] = $item->name;
                    $clients[$item->client_id]['route_name'] = $item->route_name;
                    $clients[$item->client_id]['invoices'] = [];
                }
                $clients[$item->client_id]['invoices'][] = $item;
                $clients[$item->client_id]['balance'] += $item->balance;
                $balance += floatval($item->balance);
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('receivables_by_route', $clients);
        }
        return view('advancereports.receivables_by_route', ['clients' => $clients, 'routes' => $routes]);
    }

    public function compareClientSales(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $date_history = "${from_date} - ${to_date}";
            $invoices = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'accounts.id', '=', 'clients.account_id')
                ->select(DB::raw('SUM(invoices.total_cost) AS total_cost'), DB::raw('SUM(invoices.amount) AS amount'), 'clients.name', 'clients.address1', 'clients.work_phone', 'clients.phone', 'invoices.client_id', 'clients.company_name', 'clients.seller_id', 'clients.points', 'clients.task_date', 'accounts.name as account_name')
                ->where('invoices.account_id', '<>', 6)->where('invoice_type_id', 1)
                ->where('accounts.exclude', 0)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.client_id')->get();
            $from_date = $data['start_date'];
            $to_date = $data['end_date'];
            $date_actual = "${from_date} - ${to_date}";
            $items = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'accounts.id', '=', 'clients.account_id')
                ->select(DB::raw('SUM(invoices.total_cost) AS total_cost'), DB::raw('SUM(invoices.amount) AS amount'), 'clients.name', 'clients.address1', 'clients.work_phone', 'clients.phone', 'invoices.client_id', 'clients.company_name', 'clients.seller_id', 'clients.points', 'clients.task_date', 'accounts.name as account_name')
                ->where('invoices.account_id', '<>', 6)->where('invoice_type_id', 1)
                ->where('accounts.exclude', 0)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.client_id')->get();
            $_invoices = [];
            foreach ($items as $item) {
                $_invoices[$item->client_id] = $item;
            }
            $employees = Employee::select('id', 'first_name', 'last_name', 'profile')->get()->keyBy('id');
            $result = [];
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Cliente', 'Telefono',  'Direccion', 'Empleado', 'Perfil', 'Puntos', 'Total Historico', 'Total Actual', 'Rango Inicial', 'Rango Final', 'CRM Fecha', 'Tienda'];
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($invoices as $invoice) {
                $_invoice = isset($_invoices[$invoice->client_id]) ? $_invoices[$invoice->client_id] : null;
                $employee = isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->first_name . " " . $employees[$invoice->seller_id]->last_name) : '';
                $profile = isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->profile) : '';
                if (isset($_invoices[$invoice->client_id])) {
                    $employee = isset($employees[$_invoice->seller_id]) ? ($employees[$_invoice->seller_id]->first_name . " " . $employees[$_invoice->seller_id]->last_name) : '';
                    $profile = isset($employees[$_invoice->seller_id]) ? ($employees[$_invoice->seller_id]->profile) : '';
                }
                $result = [
                    'name' => $invoice->company_name ? $invoice->company_name : $invoice->name,
                    'phone' => $invoice->work_phone ? $invoice->work_phone : $invoice->phone,
                    'address' => $invoice->address1,
                    'employee' => $employee,
                    'profile' => $profile,
                    'points' => $invoice->points,
                    'total_history' => $invoice->amount,
                    'total_actual' => isset($_invoices[$invoice->client_id]) ? $_invoices[$invoice->client_id]->amount : 'N/A',
                    'date_actual' => $date_history,
                    'date_history' => $date_actual,
                    'task_date' => $invoice->task_date,
                    'account_name' => $invoice->account_name
                ];
                fputcsv($fp, $result, ';');
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        return view('advancereports.compare_client_sales', ['result' => $result]);
    }

    public function relatedProductSales(Request $request)
    {
        $data = $request->all();
        $result = null;
        $accounts = Account::select('id', 'name')->get();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $items = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'accounts.id', '=', 'clients.account_id')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'invoice_items.product_id', '=', 'products.id')
                ->select('invoice_items.product_key', 'invoice_items.notes', 'products.relation_id', DB::raw('SUM(invoice_items.qty) as qty'), DB::raw('SUM(products.qty) as qty_global'), 'products.cost', 'products.vendor_id')
                ->where('invoices.invoice_type_id', 1)->where('accounts.exclude', 0)
                ->whereNotNull('products.relation_id')->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<=', $to_date);
            if ($request->account_id > 0) {
                $items->where('invoices.account_id', $request->account_id);
            }
            $items = $items->groupBy('invoice_items.product_key')->get();
            $result = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'accounts.id', '=', 'clients.account_id')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'invoice_items.product_id', '=', 'products.id')
                ->select('invoice_items.product_key', 'invoice_items.notes', 'products.relation_id', DB::raw('SUM(invoice_items.qty) as qty'))
                ->where('invoices.invoice_type_id', 1)->where('accounts.exclude', 0)
                ->whereNotNull('products.relation_id')->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<=', $to_date);
            if ($request->account_id > 0) {
                $result->where('invoices.account_id', $request->account_id);
            }
            $result = $result->groupBy('products.relation_id')->get();
            $products = Product::select('product_key', DB::raw('SUM(qty) as qty'), 'picture')->whereNotIn('account_id', [6, 19])->groupBy('product_key')->get();
            $_products = Product::select('relation_id', DB::raw('SUM(qty) as qty_total'))->whereNotIn('account_id', [6, 19])->groupBy('relation_id')->get()->keyBy('relation_id');
            $vendors = Vendor::select('id', 'name')->get()->keyBy('id');
            $_items = [];
            foreach ($result as $item) {
                $_items[$item->relation_id] = $item;
            }
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Codigo', 'Descripcion', 'Codigo Equivalencia', 'Unidades Vendidas', 'Equivalencias Vendidas', 'Equivalencias Globales', 'Cantidad Global', 'Costo', 'Proveedor', 'Imagen'];
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($items as &$item) {
                $item->qty_relation = isset($_items[$item->relation_id]) ? $_items[$item->relation_id]->qty : 0;
                $item->vendor_name = isset($vendors[$item->vendor_id]) ? $vendors[$item->vendor_id]->name : '';
                $fields = [
                    $item->product_key,
                    $item->notes,
                    $item->relation_id,
                    $item->qty,
                    $item->qty_relation,
                    isset($_products[$item->relation_id]) ? $_products[$item->relation_id]->qty_total : 0,
                    isset($products[$item->product_key]) ? $products[$item->product_key]->qty : 0,
                    $item->cost,
                    $item->vendor_name,
                    isset($products[$item->product_key]) && $products[$item->product_key]->picture ? 'Si' : 'No',
                ];
                fputcsv($fp, $fields, ';');
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        return view('advancereports.related_product_sales', ['result' => $result, 'accounts' => $accounts]);
    }

    public function monthlyClientSales(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $monthAgo = $data['month_ago'] == "null" ? 1 : $data['month_ago'];
            $from_date = $data['month_ago'] == "null" ? $data['from_date'] : date('Y-m-01', strtotime(date('Y-m-d') . " - {$monthAgo} months"));
            $items = [];
            for ($i = 0; $i <= $monthAgo; $i++) {
                $to_date = $data['month_ago'] == "null" ? $data['to_date'] :  date('Y-m-t', strtotime($from_date));
                $invoices = DB::table('invoices')
                    ->join('clients', 'invoices.client_id', '=', 'clients.id')
                    ->join('accounts', 'accounts.id', '=', 'clients.account_id')
                    ->select(
                        DB::raw('SUM(invoices.total_cost) AS total_cost'),
                        DB::raw('SUM(invoices.amount) AS amount'),
                        'clients.name',
                        'clients.address1',
                        'clients.work_phone',
                        'clients.phone',
                        'invoices.client_id',
                        'clients.company_name',
                        'clients.seller_id',
                        'clients.points',
                        'clients.task_date',
                        'accounts.name as account_name'
                    )
                    ->where('invoices.account_id', '<>', 6)->where('invoice_type_id', 1)
                    ->where('accounts.exclude', 0)
                    ->whereDate('invoices.invoice_date', '>=', $from_date)
                    ->whereDate('invoices.invoice_date', '<=', $to_date)
                    ->groupBy('invoices.client_id')->get();
                $items[$from_date] = $invoices;
                $from_date = date('Y-m-01', strtotime($from_date . " + 1 months"));
            }
            $employees = Employee::all()->keyBy('id');
            $monthColumns = array_keys($items);
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Cliente', 'Telefono',  'Direccion', 'Empleado', 'Perfil', 'Puntos',  'Tienda'];
            $columns = array_merge($columns, $monthColumns);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            $clients = [];
            foreach ($items as $month => $invoices) {
                foreach ($invoices as $invoice) {
                    $_invoice = isset($invoices[$invoice->client_id]) ? $invoices[$invoice->client_id] : null;
                    $employee = isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->first_name . " " . $employees[$invoice->seller_id]->last_name) : '';
                    $profile = isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->profile) : '';
                    if (isset($_invoices[$invoice->client_id])) {
                        $employee = isset($employees[$_invoice->seller_id]) ? ($employees[$_invoice->seller_id]->first_name . " " . $employees[$_invoice->seller_id]->last_name) : '';
                        $profile = isset($employees[$_invoice->seller_id]) ? ($employees[$_invoice->seller_id]->profile) : '';
                    }
                    if (isset($clients[$invoice->client_id]) == false) {
                        $clients[$invoice->client_id] = [
                            'name' => $invoice->company_name ? $invoice->company_name : $invoice->name,
                            'phone' => $invoice->work_phone ? $invoice->work_phone : $invoice->phone,
                            'address' => $invoice->address1,
                            'employee' => $employee,
                            'profile' => $profile,
                            'points' => $invoice->points,
                            'account_name' => $invoice->account_name
                        ];
                        foreach ($monthColumns as $key) {
                            $clients[$invoice->client_id][$key] = 0;
                        }
                    }
                    $clients[$invoice->client_id][$month] = $invoice->amount;
                }
            }
            foreach ($clients as $item) {
                fputcsv($fp, $item, ';');
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        return view('advancereports.monthly_client_sales', ['result' => $result]);
    }

    public function monthlySalaries(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {
            $monthAgo = $data['month_ago'];
            $from_date = date('Y-m-01', strtotime(date('Y-m-d') . " - {$monthAgo} months"));
            $items = [];
            for ($i = 0; $i <= $monthAgo; $i++) {
                $to_date = date('Y-m-t', strtotime($from_date));
                $payroll = DB::table('payroll')->join('employees', 'payroll.employee_id', '=', 'employees.id')
                    ->join('accounts', 'accounts.id', '=', 'employees.account_id')
                    ->select(DB::raw('SUM(payroll.amount + payroll.deductions + payroll.advance + payroll.loan) AS total_paid'), 'payroll.name', 'employees.profile', 'accounts.name as account_name', 'payroll.employee_id')
                    ->where('accounts.exclude', 0)
                    ->whereDate('payroll.from_date', '>=', $from_date)->whereDate('payroll.to_date', '<=', $to_date)
                    ->groupBy('payroll.employee_id')->get();
                $items[$from_date] = $payroll;
                $from_date = date('Y-m-01', strtotime($from_date . " + 1 months"));
            }
            $employees = Employee::all()->keyBy('id');
            $monthColumns = array_keys($items);
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Empleado', 'Perfil', 'Tienda'];
            $columns = array_merge($columns, $monthColumns);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            $payments = [];
            foreach ($items as $month => $data) {
                foreach ($data as $payroll) {
                    if (isset($payments[$payroll->employee_id]) == false) {
                        $payments[$payroll->employee_id] = [
                            'name' => $payroll->name,
                            'profile' => $payroll->profile,
                            'account' => $payroll->account_name,
                        ];
                        foreach ($monthColumns as $key) {
                            $payments[$payroll->employee_id][$key] = 0;
                        }
                    }
                    $payments[$payroll->employee_id][$month] = $payroll->total_paid;
                }
            }
            foreach ($payments as $item) {
                fputcsv($fp, $item, ';');
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        return view('advancereports.monthly_salaries', ['result' => $result]);
    }

    public function salesByClient(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $type = isset($data['type']) ? $data['type'] : 'Mayorista';
            $invoices = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(DB::raw('SUM(invoices.total_cost) AS total_cost'), DB::raw('SUM(invoices.amount) AS amount'), DB::raw('SUM(IF(invoices.is_credit, invoices.amount, 0)) as credit'), DB::raw('SUM(IF(invoices.is_credit, 0, invoices.amount)) as total'),  DB::raw('GROUP_CONCAT(invoices.invoice_number) as invoices'), 'clients.name', 'clients.address1', 'clients.work_phone', 'clients.phone', 'clients.id', 'clients.type', 'clients.seller_id', 'accounts.name as account_name')
                ->where('accounts.exclude', 0)->where('clients.type', $type)->where('invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.client_id')->get();
            $employees = Employee::all()->keyBy('id');
            $result = [];
            foreach ($invoices as $invoice) {
                $result[] = [
                    'name' => $invoice->name,
                    'address' => $invoice->address1,
                    'account_name' => $invoice->account_name,
                    'phone' => $invoice->work_phone ? $invoice->work_phone : $invoice->phone,
                    'employee' => isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->first_name . " " . $employees[$invoice->seller_id]->last_name) : '',
                    'profile' => isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->profile) : '',
                    'total' => $invoice->total,
                    'credit' => $invoice->credit,
                    'invoices' => $invoice->invoices,
                    'total_cost' => $invoice->total_cost
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_client', $result);
        }
        return view('advancereports.sales_by_client', ['result' => $result]);
    }

    public function salesPerClient(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $invoices = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(DB::raw('SUM(invoices.amount) AS amount'), DB::raw('SUM(IF(invoices.is_credit, invoices.amount, 0)) as credit'), DB::raw('SUM(IF(invoices.is_credit, 0, invoices.amount)) as total'), DB::raw('COUNT(*) as count'), 'accounts.name as account')
                ->where('accounts.exclude', 0)->where('invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.account_id')->groupBy('invoices.client_id')->get();
            $result = [];
            $totals = ['account' => 'Total', 'invoice_count' => 0, 'client_count' => 0, 'client_amount' => 0, 'invoice_amount' => 0];
            $totals = json_decode(json_encode($totals));
            foreach ($invoices as $item) {
                if (isset($result[$item->account]) == false) {
                    $item->client_count = 0;
                    $item->invoice_count = 0;
                    $item->client_amount = 0;
                    $item->invoice_amount = 0;
                    $result[$item->account] = $item;
                }
                $result[$item->account]->invoice_amount += $item->amount;
                $result[$item->account]->client_count += 1;
                $result[$item->account]->invoice_count += $item->count;
                $totals->client_count += 1;
                $totals->invoice_amount += $item->amount;
                $totals->invoice_count += $item->count;
            }
            $result[] = $totals;
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_per_client', $result);
        }
        return view('advancereports.sales_per_client', ['result' => $result]);
    }

    public function billing(Request $request)
    {
        $data = $request->all();
        $result = null;
        $accounts = Account::where('accounts.exclude', 0)->get();
        $result = [];
        foreach ($accounts as $account) {
            $billing = Billing::where('is_invoice', 1)->where('account_id', $account->id)->orderBy('billing_id', 'DESC')->first();
            $invoice = Invoice::where('account_id', $account->id)->orderBy('id', 'DESC')->first();;
            if (!$billing) {
                $result[] = [
                    'account' => $account->name,
                    'cai' => '',
                    'from_invoice' => '',
                    'to_invoice' => '',
                    'limit_date' => '',
                    'next_invoice' => '',
                ];
            } else {
                $result[] = [
                    'account' => $account->name,
                    'cai' => $billing->cai,
                    'from_invoice' => $billing->from_invoice,
                    'to_invoice' => $billing->to_invoice,
                    'limit_date' => $billing->limit_date,
                    'next_invoice' => $invoice ? $account->getNextInvoiceNumber($invoice) : '',
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('billing', $result);
        }
        return view('advancereports.billing', ['result' => $result]);
    }

    public function accountSettings(Request $request)
    {
        $data = $request->all();
        $result = null;
        $accounts = Account::where('accounts.exclude', 0)->get();
        $result = [];
        foreach ($accounts as $account) {
            $result[] = [
                'name' => $account->name,
                'vat_number' => (trim($account->vat_number) !== '') ? trim($account->vat_number) : 'Sin Asignar',
                'website' => (trim($account->website) !== '') ? trim($account->website) : 'Sin Asignar',
                'work_email' => (trim($account->work_email) !== '') ? trim($account->work_email) : 'Sin Asignar',
                'work_phone' => (trim($account->work_phone) !== '') ? trim($account->work_phone) : 'Sin Asignar',
                'zone' => (isset($account->zone)) ? trim($account->zone->name) : 'Sin Asignar',
                'logo' => ($account->hasLogo()) ? $account->getLogoUrl(true) : 'Sin Asignar',
                'address1' => (trim($account->address1) !== '') ? trim($account->address1) : 'Sin Asignar',
                'address2' => (trim($account->address2) !== '') ? trim($account->address2) : 'Sin Asignar',
                'city' => (trim($account->city) !== '') ? trim($account->city) : 'Sin Asignar',
                'state' => (trim($account->state) !== '') ? trim($account->state) : 'Sin Asignar',
                'postal_code' => (trim($account->postal_code) !== '') ? trim($account->postal_code) : 'Sin Asignar',
                'Matrix_name' => (trim($account->Matrix_name) !== '') ? trim($account->Matrix_name) : 'Sin Asignar',
                'Matrix_address' => (trim($account->Matrix_address) !== '') ? trim($account->Matrix_address) : 'Sin Asignar',
                'email_footer' => (trim($account->email_footer) !== '') ? trim($account->email_footer) : 'Sin Asignar',
            ];
        }
        if (isset($data['export']) && $data['export'] == 1) {
            $columns = [
                'Nombre Completo',
                'RTN',
                'Sitio Web',
                'Correo Electronico',
                'Telefono',
                'Zona',
                'Logo',
                'Direccion',
                'Bloq/Pta',
                'Ciudad',
                'Región/Provincia',
                'Código Postal',
                'Nombre Casa Matriz',
                'Dirección Casa Matriz',
                'Firma del correo',
            ];
            $bom = "\xEF\xBB\xBF";
            $fp = fopen('cofiguracion_tiendas.csv', 'w');
            fwrite($fp, $bom);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($result as $item) {
                fputcsv($fp, $item, ';');
            }
            fclose($fp);
            return redirect('/cofiguracion_tiendas.csv');
        }
        return view('advancereports.account_settings', ['result' => $result]);
    }

    public function refund_settings(Request $request)
    {
        $data = $request->all();
        $result = null;
        $accounts = Account::where('accounts.exclude', 0)->get();
        $result = [];
        foreach ($accounts as $account) {
            $billing = Billing::where('is_refund', 1)->where('account_id', $account->id)->orderBy('billing_id', 'DESC')->first();
            $refund = Refund::where('account_id', $account->id)->orderBy('id', 'DESC')->first();;
            if (!$refund) {
                $result[] = [
                    'account' => $account->name,
                    'cai' => '',
                    'from_invoice' => '',
                    'to_invoice' => '',
                    'limit_date' => '',
                    'next_invoice' => '',
                ];
            } else {
                $result[] = [
                    'account' => $account->name,
                    'cai' => $billing ? $billing->cai : '',
                    'from_invoice' => $billing ? $billing->from_invoice : '',
                    'to_invoice' => $billing ? $billing->to_invoice : '',
                    'limit_date' => $billing ? $billing->limit_date : '',
                    'next_invoice' => $refund ? $account->getNextRefundNumber($refund) : '',
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('billing', $result);
        }
        return view('advancereports.refund_settings', ['result' => $result]);
    }

    public function devices(Request $request)
    {
        $sessions = AuthenticationToken::where('logged_in', 1)->orderBy('id', 'DESC')->take(500)->get();
        return view('advancereports.devices', ['result' => $sessions]);
    }

    public function salesBySeller(Request $request)
    {
        $data = $request->all();
        $result = null;
        $accounts = Account::where('accounts.exclude', 0)->select('id', 'name')->get();
        if (count($data) > 0) {
            $currendAccountId = $data['store'];
            $currentStores = ($currendAccountId == 'all') ? array_keys($accounts->keyBy('id')->toArray()) : [(int)$currendAccountId];
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $columns = ['Vendedor', 'Tienda', 'Zona', 'Cliente', 'Telefono', 'Saldo', 'Total Facturado', 'Costo Total', 'Tipo', 'Factura', 'Fecha de Factura', 'Fecha de Pago', 'Comision', 'Perfil'];
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'sales_by_seller_' . $currentDate[0] . $currentTime . '.csv';
            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = count($currentStores);
            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'sales_by_seller';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            if ($rows == 1) {
                dispatch((new ReportSalesBySeller($nameFile, $reportProcessId, $currentStores, $from_date, $to_date))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportSalesBySeller($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'sales_by_seller')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.sales_by_seller', ['reportProcess' => $reportProcess, 'accounts' => $accounts]);
    }

    public function salesByMechanic(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $items = DB::table('invoices')->join('mechanics', 'invoices.mechanic_id', '=', 'mechanics.id')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select('mechanics.first_name', 'mechanics.last_name', 'invoices.amount', 'invoice_items.product_key', 'invoice_items.notes', 'invoices.invoice_date', 'invoices.invoice_number', 'accounts.name as account', 'invoices.balance', 'invoices.invoice_status_id', 'invoices.id', 'invoices.in_transit', 'invoices.is_credit', DB::raw('invoice_items.cost * invoice_items.qty as total'))
                ->where('accounts.exclude', 0)->where('invoices.invoice_type_id', 1)->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<', $to_date)
                ->get();
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Mecanico', 'Tienda', 'Codigo', 'Descripcion', 'Total',  'Factura', 'Fecha de Factura'];
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($items as $item) {
                $fields = [
                    'mechanic' => $item->first_name . ' ' . $item->last_name,
                    'account' => $item->account,
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'total' => $item->total,
                    'invoice_number' => $item->invoice_number,
                    'invoice_date' => $item->invoice_date,

                ];
                fputcsv($fp, $fields, ';');
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_mechanic', $result);
        }
        return view('advancereports.sales_by_mechanic', ['result' => $result]);
    }

    public function netSalesBySeller(Request $request)
    {
        $data = [];
        $result = [];
        $lastDayMonth = date('Y-m-t');
        $currentDay = date("Y-m-d");
        $firstDayMonth = date('Y-m-01');
        $halfDayMonth = date('Y-m-15');
        $dayNumber = intval(date('d', strtotime(date('Y-m-d'))));
        $from_date = strtotime($currentDay) > strtotime($halfDayMonth) ? date('Y-m-16') : $firstDayMonth;
        $to_date = strtotime($currentDay) > strtotime($halfDayMonth) ? $lastDayMonth : $halfDayMonth;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $dates = [];
        if (count($data) > 0) {
            $from_date = $request->from_date ? $request->from_date : $data['from_date'];
            $to_date =  $request->to_date ? $request->to_date : $data['to_date'];
            $dates = Payroll::select('from_date', 'to_date')->groupBy('from_date')->orderBy('from_date', 'DESC')->get();
            $items = Payroll::with(['account', 'employee'])->where('from_date', $from_date)->where('to_date', $to_date)->groupBy('employee_id')->get();
            foreach ($items as $item) {
                $is_admin = $item->employee->profile == 'ADMINISTRADOR DE TIENDA';
                if ($is_admin) {
                    $store_credit = StoreCredit::where('account_id', $item->employee->account_id)
                        ->whereDate('created_at', '>=', $from_date)
                        ->whereDate('created_at', '<=', $to_date)->sum('amount');
                } else {
                    $store_credit = StoreCredit::where('employee_id', $item->employee_id)
                        ->whereDate('created_at', '>=', $from_date)
                        ->whereDate('created_at', '<=', $to_date)->sum('amount');
                }
                $result[] = [
                    'employee' => $item->name,
                    'profile' => $item->employee ? $item->employee->profile : '',
                    'account' => $item->account ? $item->account->name : '',
                    'base_sales' => $item->base_sales,
                    'refunds' => $item->refunds,
                    'sales' => $item->sales,
                    'commission_amount' => $item->commission_amount,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'store_credit' => $store_credit ?? 0
                ];
            }
        }
        if ($request->export) {
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $nameFile = 'ventas_' . $currentDate[0] . '.csv';
            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'w');
            fwrite($fp, $bom);
            $columns = ['Vendedor',  'Perfil', 'Tienda', 'Ventas Base', 'Credito de Tienda',  'Devoluciones', 'Ventas Netas', 'Comision', 'Fecha Inicio', 'Fecha Fin'];
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($items as $item) {
                $fields = [
                    'employee' => $item->name,
                    'profile' => $item->employee ? $item->employee->profile : '',
                    'account' => $item->account ? $item->account->name : '',
                    'base_sales' => $item->base_sales,
                    'store_credit' => $item->store_credit,
                    'refunds' => $item->refunds,
                    'sales' => $item->sales,
                    'commission_amount' => $item->commission_amount,
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ];
                fputcsv($fp, $fields, ';');
            }
            fclose($fp);
            return redirect('/' . $nameFile);
        }
        return view('advancereports.net_sales_by_seller', ['result' => $result, 'dates' => $dates]);
    }

    public function paymentsBySeller(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $start = date('Y-m-d', strtotime('-6 months'));
            $invoices = DB::table('payments')
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('employees', 'invoices.employee_id', '=', 'employees.id')
                ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select('payments.amount', 'payments.payment_date', 'employees.first_name', 'employees.last_name', 'invoices.total', 'invoices.commission', 'invoices.invoice_date', 'invoices.invoice_number', 'accounts.name as account', 'invoices.balance', 'invoices.invoice_status_id', 'employees.profile', 'invoices.id', 'clients.name as client', 'invoices.in_transit', 'invoices.is_credit', 'invoices.amount as total_amount')
                ->where('accounts.exclude', 0)->where('invoice_type_id', 1)
                ->whereDate('payments.payment_date', '>=', $from_date)->whereDate('payments.payment_date', '<', $to_date)
                ->get();
            $items = InvoiceItem::select('invoice_id', DB::raw('SUM(product_cost * qty) as total_cost'))
                ->whereDate('created_at', '>=', $start)
                ->groupBy('invoice_id')->get()->keyBy('invoice_id');
            $result = [];
            foreach ($invoices as $invoice) {
                $result[] = [
                    'employee' => $invoice->first_name . ' ' . $invoice->last_name,
                    'account' => $invoice->account,
                    'client' => $invoice->client,
                    'balance' => $invoice->balance,
                    'amount' => $invoice->amount,
                    'total_amount' => $invoice->total_amount,
                    'total_cost' => isset($items[$invoice->id]) ? $items[$invoice->id]->total_cost : 0,
                    'invoice_status' => $invoice->in_transit ? 'En transito' : ($invoice->is_credit ? 'Credito' : 'Contado'),
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'payment_date' => $invoice->payment_date,
                    'commission' => $invoice->commission,
                    'profile' => $invoice->profile,
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('payments_by_seller', $result);
        }
        return view('advancereports.payments_by_seller', ['result' => $result]);
    }

    public function salesByVendor(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {

            $monthAgo = $data['month_ago'] == "null" ? 1 : $data['month_ago'];
            $store = (isset($data['store']) && $data['store'] !== 'all') ? $data['store'] : null;

            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');

            $from_date = $data['month_ago'] == "null" ? $data['from_date'] : date('Y-m-01', strtotime(date('Y-m-d') . " - {$monthAgo} months"));
            $start = Carbon::parse($from_date)->startOfMonth();
            $from_date = $start->format('Y-m-d');

            $to_date = $data['month_ago'] == "null" ? $data['to_date'] :  date('Y-m-t', strtotime($current_date));
            $end = Carbon::parse($to_date)->endOfMonth();
            $to_date = $end->format('Y-m-d');

            $period = new CarbonPeriod($start, '1 month', $end);
            $columns = [];
            $data_tracking = [];
            foreach ($period as $dt) {
                $_date = $dt->format('Y-m');
                $columns[$_date] = [
                    'qty' => 0,
                    'total' => 0,
                    'cost' => 0,
                ];
            }
            $columns['total'] = [
                'qty' => 0,
                'total' => 0,
                'cost' => 0
            ];

            $invoices = [];

            $invoices = DB::table('invoices')->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select(
                    'invoice_items.qty',
                    'invoice_items.cost',
                    'invoice_items.product_cost',
                    'vendors.name',
                    'products.vendor_id',
                    DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m") as invoice_month')
                )
                ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                ->where('invoices.invoice_type_id', 1)->whereDate('invoices.created_at', '>=', $from_date)
                ->whereDate('invoices.created_at', '<=', $to_date);
            if ($store) {
                $invoices = $invoices->where('invoices.account_id', $store);
            }
            $invoices = $invoices->get();
            $result = [];

            foreach ($invoices as $item) {
                $vendor = (isset($item->name) && trim($item->name) !== '') ? $item->name : 'Por Definir';
                if (isset($result[$vendor]) == false) {
                    $result[$vendor] = $columns;
                }

                if (isset($result[$vendor][$item->invoice_month]) == false) {
                    $result[$vendor][$item->invoice_month] = [
                        'qty' => 0,
                        'total' => 0,
                        'cost' => 0
                    ];
                }
                $result[$vendor][$item->invoice_month]['qty'] += intval($item->qty);
                $result[$vendor][$item->invoice_month]['cost'] += (intval($item->qty) * floatval($item->product_cost));
                $result[$vendor][$item->invoice_month]['total'] += (intval($item->qty) * floatval($item->cost));

                if (isset($result[$vendor]['total']) == false) {
                    $result[$vendor]['total'] = [
                        'qty' => 0,
                        'total' => 0,
                        'cost' => 0
                    ];
                }

                $result[$vendor]['total']['qty'] += intval($item->qty);
                $result[$vendor]['total']['cost'] += (intval($item->qty) * floatval($item->product_cost));
                $result[$vendor]['total']['total'] += (intval($item->qty) * floatval($item->cost));
            }
            $name = 'ventas_por_proveedor_' . str_replace('-', '_', $current_date) . '.csv';
            $fp = fopen($name, 'w');
            $head = ['Proveedor'];
            foreach ($columns as $key3 => $value3) {
                $head[] = $key3 . ' Unidades';
                $head[] = $key3 . ' Monto';
                $head[] = $key3 . ' Costo';
            }
            fputcsv($fp, $head, ';');
            foreach ($result as $key => $value) {
                $data = [
                    $key,
                ];
                foreach ($columns as $key3 => $value3) {
                    if (isset($value[$key3])) {
                        $data[] = isset($value[$key3]['qty']) ? $value[$key3]['qty'] : 0;
                        $data[] = isset($value[$key3]['cost']) ? $value[$key3]['cost'] : 0;
                        $data[] = isset($value[$key3]['total']) ? $value[$key3]['total'] : 0;
                    } else {
                        $data[] = 0;
                        $data[] = 0;
                        $data[] = 0;
                    }
                }
                fputcsv($fp, $data, ';');
            }
            fclose($fp);
            return redirect('/' . $name);
        }
        return view('advancereports.sales_by_vendor', ['result' => $result, 'stores' => $stores]);
    }

    public function salesUtilityByDate(Request $request)
    {
        $is_root = in_array(Auth::user()->realUser()->id, Auth::user()->root) ? true : null;
        $_result = null;
        return view('advancereports.sales_by_date', ['result' => $_result, 'is_root' => $is_root]);
    }

    public function salesByDate(Request $request)
    {
        $data = $request->all();
        $result = null;
        $_result = null;
        $columns = [];
        $accounts = Account::where('exclude', 0)->get()->keyBy('id');
        $totals = ['sale_amount' => 0, 'sale_cost' => 0];
        if (count($data) > 0) {
            $is_root =  isset($data['is_root']) ? true : null;
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $date1 = new \DateTime($from_date);
            $date2 = new \DateTime($to_date);
            $days  = $date2->diff($date1)->format('%a');
            for ($i = 0; $i < $days; $i++) {
                $newDate = strtotime($from_date . " + $i days");
                $_date = date('Y-m-d', $newDate);
                $columns[$_date] = [
                    'total' => 0,
                    'oil' => 0,
                    'oil_wholesaler' => 0,
                    'total_refunded' => 0,
                    'total_cost' => 0,
                    'sale_amount' => 0,
                    'sale_cost' => 0
                ];
            }
            $group = isset($data['group']) ? $data['group'] : null;
            $invoices = DB::table('invoices')->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(
                    DB::raw('SUM(invoices.replacement_amount) as total'),
                    DB::raw('SUM(total_refunded) as total_refunded'),
                    DB::raw('SUM(invoices.oil) as oil'),
                    DB::raw('SUM(IF(invoices.client_type = "Mayorista", invoices.oil_amount, 0)) as oil_wholesaler'),
                    DB::raw('SUM(invoices.oil_amount) as oil_amount'),
                    DB::raw('SUM(invoices.replacement_amount) as replacement_amount'),
                    DB::raw('SUM(invoices.total_cost) as total_cost'),
                    DB::raw('SUM(invoices.amount) as sale_amount'),
                    'accounts.name as account',
                    'accounts.id as account_id',
                    'invoices.invoice_date'
                )->where('accounts.exclude', 0)->where('invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.account_id', 'invoices.invoice_date')->get();
            $refundItems = DB::table('refunds')->select(
                DB::raw('SUM(oil_amount + replacement_amount) as total_refunded'),
                'account_id',
                'refund_date'
            )
                ->whereDate('refund_date', '>=', $from_date)->whereDate('refund_date', '<', $to_date)
                ->groupBy('account_id', 'refund_date')->get();
            $refunds = [];
            foreach ($refundItems as $item) {
                if (isset($refunds[$item->account_id]) === false) {
                    $refunds[$item->account_id] = [];
                }
                $refunds[$item->account_id][$item->refund_date] = floatval($item->total_refunded);
            }
            $result = [];
            $totals = ['sale_amount' => 0, 'sale_cost' => 0];
            foreach ($invoices as $invoice) {
                if (isset($result[$invoice->account_id]) == false) {
                    $result[$invoice->account_id] = $columns;
                }
                if (isset($result[$invoice->account_id][$invoice->invoice_date]) !== false) {
                    $result[$invoice->account_id][$invoice->invoice_date]['total'] = floatval($invoice->total);
                    $result[$invoice->account_id][$invoice->invoice_date]['total_refunded'] = 0;
                    if (isset($refunds[$invoice->account_id]) !== false && isset($refunds[$invoice->account_id][$invoice->invoice_date])) {
                        $result[$invoice->account_id][$invoice->invoice_date]['total_refunded'] += $refunds[$invoice->account_id][$invoice->invoice_date];
                    }
                    $result[$invoice->account_id][$invoice->invoice_date]['oil'] = floatval($invoice->oil);
                    $result[$invoice->account_id][$invoice->invoice_date]['oil_wholesaler'] = floatval($invoice->oil_wholesaler);
                    $result[$invoice->account_id][$invoice->invoice_date]['sale_amount'] = floatval($invoice->sale_amount);
                    $result[$invoice->account_id][$invoice->invoice_date]['sale_cost'] = floatval($invoice->total_cost);
                    $result[$invoice->account_id][$invoice->invoice_date]['total_cost'] = floatval($invoice->total) - floatval($invoice->total_cost);
                }
            }
            $_result = ['columns' => $columns, 'values' => [], 'totals' => $totals, 'is_root' => $is_root];
            foreach ($result as $key => $items) {
                $name = $accounts[$key]->name;
                $_result['values'][$name] = $items;
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_date', $_result);
        }
        return view('advancereports.sales_by_date', ['result' => $_result]);
    }

    public function salesByMonth(Request $request)
    {
        $data = $request->all();
        $result = null;
        $_result = null;
        $columns = [];
        $accounts = Account::where('exclude', 0)->get()->keyBy('id');
        $totals = ['sale_amount' => 0, 'sale_cost' => 0];
        if (count($data) > 0) {
            $is_root =  isset($data['is_root']) ? true : null;
            $from_date = $data['from_date'];
            $start = Carbon::parse($from_date)->startOfMonth();
            $from_date = $start->format('Y-m-d');
            $to_date = $data['to_date'];
            $end = Carbon::parse($to_date)->endOfMonth();
            $to_date = $end->format('Y-m-d');
            $period = new CarbonPeriod($start, '1 month', $end);
            foreach ($period as $dt) {
                $_date = $dt->format('Y-m');
                $columns[$_date] = [
                    'total' => 0,
                    'oil' => 0,
                    'oil_wholesaler' => 0,
                    'total_refunded' => 0,
                    'total_cost' => 0,
                    'sale_amount' => 0,
                    'sale_cost' => 0
                ];
            }
            $columns['total'] = [
                'total' => 0,
                'oil' => 0,
                'oil_wholesaler' => 0,
                'total_refunded' => 0,
                'total_cost' => 0,
                'sale_amount' => 0,
                'sale_cost' => 0,
                'result' => 0
            ];
            $invoices = DB::table('invoices')->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(
                    DB::raw('SUM(invoices.replacement_amount) as total'),
                    DB::raw('SUM(total_refunded) as total_refunded'),
                    DB::raw('SUM(invoices.oil) as oil'),
                    DB::raw('SUM(IF(invoices.client_type = "Mayorista", invoices.oil_amount, 0)) as oil_wholesaler'),
                    DB::raw('SUM(invoices.oil_amount) as oil_amount'),
                    DB::raw('SUM(invoices.replacement_amount) as replacement_amount'),
                    DB::raw('SUM(invoices.total_cost) as total_cost'),
                    DB::raw('SUM(invoices.amount) as sale_amount'),
                    'accounts.name as account',
                    'accounts.id as account_id',
                    DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m") as invoice_month')
                )->where('accounts.exclude', 0)->where('invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.account_id', DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m")'))->get();

            $refundItems = DB::table('refunds')
                ->select(
                    DB::raw('SUM(oil_amount + replacement_amount) as total_refunded'),
                    'account_id',
                    DB::raw('DATE_FORMAT(refund_date, "%Y-%m") as refund_month')
                )->whereDate('refund_date', '>=', $from_date)->whereDate('refund_date', '<', $to_date)
                ->groupBy('account_id', DB::raw('DATE_FORMAT(refund_date, "%Y-%m")'))->get();

            $refunds = [];
            foreach ($refundItems as $item) {
                if (isset($refunds[$item->account_id]) === false) {
                    $refunds[$item->account_id] = [];
                }
                $refunds[$item->account_id][$item->refund_month] = floatval($item->total_refunded);
            }
            $result = [];
            $totals = ['sale_amount' => 0, 'sale_cost' => 0];
            foreach ($invoices as $invoice) {
                if (isset($result[$invoice->account_id]) == false) {
                    $result[$invoice->account_id] = $columns;
                }
                if (isset($result[$invoice->account_id][$invoice->invoice_month]) !== false) {
                    $result[$invoice->account_id][$invoice->invoice_month]['total'] = floatval($invoice->total);
                    $result[$invoice->account_id][$invoice->invoice_month]['total_refunded'] = 0;
                    if (isset($refunds[$invoice->account_id]) !== false && isset($refunds[$invoice->account_id][$invoice->invoice_month])) {
                        $result[$invoice->account_id][$invoice->invoice_month]['total_refunded'] += $refunds[$invoice->account_id][$invoice->invoice_month];
                    }
                    $result[$invoice->account_id][$invoice->invoice_month]['oil'] = floatval($invoice->oil);
                    $result[$invoice->account_id][$invoice->invoice_month]['oil_wholesaler'] = floatval($invoice->oil_wholesaler);
                    $result[$invoice->account_id][$invoice->invoice_month]['sale_amount'] = floatval($invoice->sale_amount);
                    $result[$invoice->account_id][$invoice->invoice_month]['sale_cost'] = floatval($invoice->total_cost);
                    $result[$invoice->account_id][$invoice->invoice_month]['total_cost'] = floatval($invoice->total) - floatval($invoice->total_cost);

                    $result[$invoice->account_id]['total']['total'] += $result[$invoice->account_id][$invoice->invoice_month]['total'];
                    $result[$invoice->account_id]['total']['total_refunded'] += $result[$invoice->account_id][$invoice->invoice_month]['total_refunded'];
                    $result[$invoice->account_id]['total']['oil'] += $result[$invoice->account_id][$invoice->invoice_month]['oil'];
                    $result[$invoice->account_id]['total']['oil_wholesaler'] += $result[$invoice->account_id][$invoice->invoice_month]['oil_wholesaler'];
                    $result[$invoice->account_id]['total']['sale_amount'] += $result[$invoice->account_id][$invoice->invoice_month]['sale_amount'];
                    $result[$invoice->account_id]['total']['sale_cost'] += $result[$invoice->account_id][$invoice->invoice_month]['sale_cost'];
                    $result[$invoice->account_id]['total']['total_cost'] += $result[$invoice->account_id][$invoice->invoice_month]['total_cost'];
                    $result[$invoice->account_id]['total']['result'] +=
                        $result[$invoice->account_id][$invoice->invoice_month]['total'] +
                        $result[$invoice->account_id][$invoice->invoice_month]['oil'] -
                        $result[$invoice->account_id][$invoice->invoice_month]['total_refunded'];
                }
            }
            $_result = ['columns' => $columns, 'values' => [], 'totals' => $totals, 'is_root' => $is_root];
            foreach ($result as $key => $items) {
                $name = $accounts[$key]->name;
                $_result['values'][$name] = $items;
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_month', $_result);
        }
        return view('advancereports.sales_by_month', ['result' => $_result]);
    }

    public function salesByDateSum(Request $request)
    {
        $data = $request->all();
        $result = null;
        $_result = null;
        $columns = [];
        $accounts = Account::where('exclude', 0)->get()->keyBy('id');
        $totals = ['sale_amount' => 0, 'sale_cost' => 0];
        if (count($data) > 0) {
            $is_root =  isset($data['is_root']) ? true : null;
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $date1 = new \DateTime($from_date);
            $date2 = new \DateTime($to_date);
            $days  = $date2->diff($date1)->format('%a');
            for ($i = 0; $i < $days; $i++) {
                $newDate = strtotime($from_date . " + $i days");
                $_date = date('Y-m-d', $newDate);
                $columns[$_date] = [
                    'total' => 0,
                    'oil' => 0,
                    'oil_wholesaler' => 0,
                    'total_refunded' => 0,
                    'total_cost' => 0,
                    'sale_amount' => 0,
                    'sale_cost' => 0
                ];
            }
            $group = isset($data['group']) ? $data['group'] : null;
            $invoices = DB::table('invoices')->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(DB::raw('SUM(invoices.replacement_amount) as total'), DB::raw('SUM(total_refunded) as total_refunded'), DB::raw('SUM(invoices.oil) as oil'), DB::raw('SUM(IF(invoices.client_type = "Mayorista", invoices.oil_amount, 0)) as oil_wholesaler'), DB::raw('SUM(invoices.oil_amount) as oil_amount'), DB::raw('SUM(invoices.replacement_amount) as replacement_amount'), DB::raw('SUM(invoices.total_cost) as total_cost'), DB::raw('SUM(invoices.amount) as sale_amount'), 'accounts.name as account', 'accounts.id as account_id', 'invoices.invoice_date')
                ->where('accounts.exclude', 0)->where('invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.account_id', 'invoices.invoice_date')->get();
            $refundItems = DB::table('refunds')->select(DB::raw('SUM(oil_amount + replacement_amount) as total_refunded'), 'account_id', 'refund_date')
                ->whereDate('refund_date', '>=', $from_date)->whereDate('refund_date', '<', $to_date)
                ->groupBy('account_id', 'refund_date')->get();
            $refunds = [];
            foreach ($refundItems as $item) {
                if (isset($refunds[$item->account_id]) === false) {
                    $refunds[$item->account_id] = [];
                }
                $refunds[$item->account_id][$item->refund_date] = floatval($item->total_refunded);
            }
            $result = [];
            $totals = ['sale_amount' => 0, 'sale_cost' => 0];
            foreach ($invoices as $invoice) {
                if (isset($result[$invoice->account_id]) == false) {
                    $result[$invoice->account_id] = $columns;
                }
                if (isset($result[$invoice->account_id][$invoice->invoice_date]) !== false) {
                    $result[$invoice->account_id][$invoice->invoice_date]['total'] = floatval($invoice->total);
                    $result[$invoice->account_id][$invoice->invoice_date]['total_refunded'] = 0;
                    if (isset($refunds[$invoice->account_id]) !== false && isset($refunds[$invoice->account_id][$invoice->invoice_date])) {
                        $result[$invoice->account_id][$invoice->invoice_date]['total_refunded'] += $refunds[$invoice->account_id][$invoice->invoice_date];
                    }
                    $result[$invoice->account_id][$invoice->invoice_date]['oil'] = floatval($invoice->oil);
                    $result[$invoice->account_id][$invoice->invoice_date]['oil_wholesaler'] = floatval($invoice->oil_wholesaler);
                    $result[$invoice->account_id][$invoice->invoice_date]['sale_amount'] = floatval($invoice->sale_amount);
                    $result[$invoice->account_id][$invoice->invoice_date]['sale_cost'] = floatval($invoice->total_cost);
                    $result[$invoice->account_id][$invoice->invoice_date]['total_cost'] = floatval($invoice->total) - floatval($invoice->total_cost);
                }
            }
            $_result = ['columns' => $columns, 'values' => [], 'totals' => $totals, 'is_root' => $is_root];
            foreach ($result as $key => $items) {
                $name = $accounts[$key]->name;
                $_result['values'][$name] = $items;
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_date_sum', $_result);
        }
        return view('advancereports.sales_by_date_sum', ['result' => $_result]);
    }

    public function stockByVendor(Request $request)
    {
        $data = $request->all();
        $is_root = in_array(Auth::user()->realUser()->id, Auth::user()->root) ? true : false;
        $vendors = Vendor::select('id', 'name')->get();
        $accounts = Account::select('id', 'name')->get();
        $_vendors = $vendors->keyBy('id')->toArray();
        $_accounts = $accounts->keyBy('id')->toArray();
        if (count($data) > 0) {
            $vendor = $data['vendor'] > 0 ? $data['vendor'] : null;
            if (is_null($vendor)) {
                Session::flash('message', "No a seleccionado ningun proveedor");
                return redirect()->back();
            }
            $group = $data['group'] > 0 ? true : false;
            if ($is_root) {
                $columns = ['Codigo', 'Producto', 'Proveedor', 'Costo', 'Precio Final', 'Precio Mayorista', 'Precio Especial', 'Unidades', 'Cantidad Global', 'Tiendas', 'Fecha Actualizacion', 'Equivalencia', 'Equivalencia Global', 'Ventas Equivalencia'];
            } else {
                $columns = ['Codigo', 'Producto', 'Proveedor', 'Precio Final', 'Precio Mayorista', 'Precio Especial', 'Unidades', 'Cantidad Global', 'Tiendas', 'Fecha Actualizacion', 'Equivalencia', 'Equivalencia Global', 'Ventas Equivalencia'];
            }
            $productCount = Product::where('vendor_id', $vendor)
                ->count();
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'stock_by_vendor_' . $currentDate[0] . $currentTime . '.csv';

            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = intval(ceil($productCount / 400));
            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'stock_by_vendor';
            $reportProcess->status = 0;
            $reportProcess->rows = $rows;
            $reportProcess->count_rows = 0;
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            $count = 1;

            for ($i = 1; $i < $productCount; $i = $i + 400) {
                $chunk = null;
                $init = ($i == 1) ? 0 : $i;
                $chunk = ['skip' => $init, 'take' => $i + 399];
                dispatch((new ReportStockByVendor($chunk, $nameFile, $group, $_vendors, $_accounts, $is_root, $reportProcessId, $vendor))->delay(60 * $count));
                $count = $count + 1;
            }
        }
        $reportProcess = ReportProcess::where('report', 'stock_by_vendor')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.stock_by_vendor', ['vendors' => $vendors, 'is_root' => $is_root, 'reportProcess' => $reportProcess]);
    }

    public function saleItemsByVendor(Request $request)
    {

        $data = $request->all();
        $_vendors = Vendor::all()->keyBy('id');
        $_accounts = Account::all()->keyBy('id');
        $_employees = Employee::all()->keyBy('id');
        $result = null;
        if (count($data) > 0) {
            $startDate  = Carbon::now()->subMonths(6);
            $endDate = Carbon::now();

            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $vendor_id = isset($data['vendor_id']) ? $data['vendor_id'] : null;
            $group = $data['group'] > 0  ? true : false;

            $itemsAccount = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->select([
                    'invoices.account_id',
                    'invoice_items.product_key',
                    'invoice_items.qty',
                    'products.relation_id'
                ])
                ->where('invoices.invoice_type_id', 1)
                ->where('invoices.invoice_date', '>=', $startDate)
                ->where('invoices.invoice_date', '<', $endDate)
                ->whereNull('invoice_items.deleted_at')
                ->get();
            $qtySalesProducts = [];
            $qtySalesRelatedId = [];
            foreach ($itemsAccount as $item) {
                if (!isset($qtySalesProducts[$item->product_key])) {
                    $qtySalesProducts[$item->product_key] = 0;
                }
                if (!isset($qtySalesRelatedId[$item->relation_id])) {
                    $qtySalesRelatedId[$item->relation_id] = 0;
                }
                if (isset($item->product_key) && trim($item->product_key) != '') {
                    $qtySalesProducts[$item->product_key] += $item->qty;
                }
                if (isset($item->relation_id) && trim($item->relation_id) != '') {
                    $qtySalesRelatedId[$item->relation_id] += $item->qty;
                }
            }

            $query = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select(
                    'products.product_key',
                    'products.notes',
                    'products.qty as availableQty',
                    'products.vendor_id',
                    'products.updated_at',
                    'invoices.account_id',
                    'invoices.invoice_number',
                    'invoices.invoice_date',
                    'invoices.employee_id',
                    'invoices.in_transit',
                    'invoices.is_credit',
                    'invoice_items.qty',
                    'invoice_items.qty_refunded',
                    'invoice_items.cost as price',
                    'invoice_items.product_cost as cost',
                    'clients.name',
                    'products.relation_id'
                )
                ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->where('invoices.invoice_type_id', 1);
            if ($vendor_id) {
                $query->where('vendor_id', $vendor_id);
            }
            $items = $query->get();
            $products = Product::select('id', 'product_key', 'qty', 'cost', 'relation_qty')->where('account_id', 17)->get()->keyBy('product_key');
            $_products = Product::select('product_key', DB::raw('SUM(qty) as qty'))
                ->whereNotIn('account_id', [6, 19])
                ->groupBy('product_key')
                ->get()->keyBy('product_key');
            $relatedProducts = Product::select('relation_id', DB::raw('SUM(qty) as qty'))
                ->whereNotNull('relation_id')
                ->whereNotIn('account_id', [6, 19])
                ->groupBy('relation_id')
                ->get()->keyBy('relation_id');
            $relationIds = array_keys($relatedProducts->toArray());
            $sales = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('products.relation_id', DB::raw('SUM(invoice_items.qty) as total'))
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->where('accounts.exclude', 0)
                ->whereNotNull('products.relation_id')
                ->whereIn('products.relation_id', $relationIds)
                ->groupBy('products.relation_id')
                ->get();
            $sales = collect($sales)->keyBy('relation_id');
            $result = [];
            $product = null;
            foreach ($items as $item) {
                if ($group) {
                    if (isset($result[$item->product_key]) == false) {
                        $result[$item->product_key] = [
                            'product_key' => $item->product_key,
                            'invoice_number' => $item->invoice_number,
                            'relation_id' => isset($item->relation_id) ? $item->relation_id : 0,
                            'payment_type' => $item->in_transit ? 'En transito' : ($item->is_credit ? 'Credito' : 'Contado'),
                            'client' => $item->name,
                            'notes' => $item->notes,
                            'cost' => $item->cost,
                            'price' => $item->price,
                            'availableQty' => isset($_products[$item->product_key]) ? $_products[$item->product_key]->qty : 0,
                            'qtyInWarehouse' => isset($products[$item->product_key]) ? $products[$item->product_key]->qty : 0,
                            'relation_qty' => isset($products[$item->product_key]) ? $products[$item->product_key]->relation_qty : 0,
                            'relation_qty_global' => isset($relatedProducts[$item->relation_id]) ? $relatedProducts[$item->relation_id]->qty : 0,
                            'relation_sales' => isset($sales[$item->relation_id]) ? $sales[$item->relation_id]->total : 0,
                            'qty' => 0,
                            'qty_refunded' => 0,
                            'total' => 0,
                            'total_refunded' => 0,
                            'total_cost' => 0,
                            'qty_sales_products' =>
                            isset($qtySalesProducts[$item->product_key]) ?
                                intval(ceil($qtySalesProducts[$item->product_key] / 6)) : 0,
                            'qty_sales_related' =>
                            isset($qtySalesRelatedId[$item->relation_id]) ?
                                intval(ceil($qtySalesRelatedId[$item->relation_id] / 6)) : 0,
                            'vendor' => isset($_vendors[$item->vendor_id]) ? $_vendors[$item->vendor_id]->name : '',
                            'seller' => isset($_employees[$item->employee_id]) ? $_employees[$item->employee_id]->first_name . " " . $_employees[$item->employee_id]->last_name : '',
                            'accounts' => []
                        ];
                    }
                    $result[$item->product_key]['qty'] += intval($item->qty);
                    $result[$item->product_key]['qty_refunded'] += intval($item->qty_refunded);
                    $result[$item->product_key]['total'] += (intval($item->qty) * floatval($item->price));
                    $result[$item->product_key]['total_refunded'] += (intval($item->qty_refunded) * floatval($item->price));
                    $result[$item->product_key]['total_cost'] += (intval($item->qty) * floatval($item->cost));
                    $result[$item->product_key]['accounts'][$item->account_id] = $_accounts[$item->account_id]->name;
                } else {
                    $result[] = [
                        'product_key' => $item->product_key,
                        'invoice_number' => $item->invoice_number,
                        'relation_id' => isset($item->relation_id) ? $item->relation_id : 0,
                        'payment_type' => $item->in_transit ? 'En transito' : ($item->is_credit ? 'Credito' : 'Contado'),
                        'client' => $item->name,
                        'notes' => $item->notes,
                        'cost' => $item->cost,
                        'price' => $item->price,
                        'availableQty' => isset($_products[$item->product_key]) ? $_products[$item->product_key]->qty : 0,
                        'qtyInWarehouse' => isset($products[$item->product_key]) ? $products[$item->product_key]->qty : 0,
                        'relation_qty' => isset($products[$item->product_key]) ? $products[$item->product_key]->relation_qty : 0,
                        'relation_qty_global' => isset($relatedProducts[$item->relation_id]) ? $relatedProducts[$item->relation_id]->qty : 0,
                        'relation_sales' => isset($sales[$item->relation_id]) ? $sales[$item->relation_id]->total : 0,
                        'qty' => $item->qty,
                        'qty_refunded' => $item->qty_refunded,
                        'total' => $item->price * $item->qty,
                        'total_refunded' => $item->price * $item->qty_refunded,
                        'total_cost' => $item->cost * $item->qty,
                        'vendor' => isset($_vendors[$item->vendor_id]) ? $_vendors[$item->vendor_id]->name : '',
                        'seller' => isset($_employees[$item->employee_id]) ? $_employees[$item->employee_id]->first_name . " " . $_employees[$item->employee_id]->last_name : '',
                        'accounts' => [$_accounts[$item->account_id]->name],
                        'updated_at' => $item->updated_at ? date('Y-m-d H:i:s', strtotime($item->updated_at)) : '',
                        'qty_sales_products' =>
                        isset($qtySalesProducts[$item->product_key]) ?
                            intval(ceil($qtySalesProducts[$item->product_key] / 6)) : 0,
                        'qty_sales_related' =>
                        isset($qtySalesRelatedId[$item->relation_id]) ?
                            intval(ceil($qtySalesRelatedId[$item->relation_id] / 6)) : 0,
                    ];
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('saleitems_by_vendor', $result);
        }
        return view('advancereports.saleitems_by_vendor', ['result' => $result, 'vendors' => $_vendors]);
    }

    public function salesByProduct(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $categories = Category::select(['category_id', 'name'])->get()->keyBy('category_id');
            $products = Product::where('account_id', 17)->select(['product_key', 'qty'])->get()->keyBy('product_key');

            $invoices = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select([
                    'products.product_key',
                    'products.notes',
                    'products.category_id',
                    'invoice_items.qty',
                    'invoices.account_id',
                    'invoice_items.cost',
                    'products.relation_id'
                ])
                ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date);
            if (isset($store) && $store > 0) {
                $invoices = $invoices->where('invoices.account_id', $store);
            }
            $invoices = $invoices->get();
            $accountsNotExclude = Account::where('exclude', 0)->pluck('id')->toArray();
            $productsGlobal = Product::whereIn('product_key', collect($invoices)->pluck('product_key')->toArray())
                ->whereIn('account_id', $accountsNotExclude)
                ->select([
                    'product_key',
                    DB::raw("SUM(qty) as qty")
                ])
                ->groupBy('product_key')
                ->get()->keyBy('product_key');

            $relationsQty = [];
            foreach ($invoices as $item) {
                if (isset($relationsQty[$item->relation_id]) === false) {
                    $relationsQty[$item->relation_id] = 0;
                }
                $relationsQty[$item->relation_id] += $item->qty;
            }

            $result = [];
            foreach ($invoices as $item) {
                if (isset($result[$item->product_key]) == false) {
                    $result[$item->product_key] = [
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'qtyInWarehouse' => isset($products[$item->product_key]) ? $products[$item->product_key]->qty : 0,
                        'qty' => 0,
                        'total' => 0,
                        'category' => $categories[$item->category_id]->name,
                        'relation_id' => $item->relation_id,
                        'relation_qty_sales' => isset($relationsQty[$item->relation_id]) ? $relationsQty[$item->relation_id] : 0,
                        'global_qty' => isset($productsGlobal[$item->relation_id]) ? $productsGlobal[$item->relation_id]->qty : 0,
                    ];
                }
                $result[$item->product_key]['qty'] += intval($item->qty);
                $result[$item->product_key]['total'] += (intval($item->qty) * floatval($item->cost));
            }

            $columns = [
                'Codigo',
                'Descripcion',
                'Cantidad en Bodega',
                'Unidades Vendidas',
                'Monto Vendido',
                'Categoria',
                'Equivalencias',
                'Cantidad de Equivalencias Vendidas',
                'Cantidad Global'
            ];

            $filePath = 'reportes_avanzados_productos_vendidos.csv';
            $fp = fopen($filePath, 'w');
            fputcsv($fp, $columns, ';');
            fputcsv($fp, [], ';');
            foreach ($result as $data) {
                fputcsv($fp, $data, ';');
            }
            fclose($fp);
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        return view('advancereports.sales_by_product', ['result' => $result, 'stores' => $stores]);
    }

    public function packingToInvoice(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $packings = DB::table('packings')
                ->where(function ($query) {
                    $query->whereNotNull('invoice_id')
                        ->orWhereNotNull('transfer_id');
                })
                ->where('packings.account_id', Auth::user()->account_id)
                ->whereDate('packings.created_at', '>=', $from_date)->whereDate('packings.created_at', '<', $to_date)
                ->get();
            $result = [];
            foreach ($packings as $item) {
                $result[] = [
                    'packing' => str_pad($item->id, 8, "0", STR_PAD_LEFT),
                    'date' => $item->created_at,
                    'items_qty' => $item->items_qty,
                    'user' => $item->user_name,
                    'invoice' => $item->invoice_number,
                    'transfer' => $item->transfer_id,
                    'quote' => $item->quote_number
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('packing_to_invoice', $result);
        }
        return view('advancereports.packing_to_invoice', ['result' => $result]);
    }

    public function packingToTransfer(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $packings = DB::table('packings')->whereNotNull('transfer_id')
                ->where('packings.account_id', Auth::user()->account_id)
                ->whereDate('packings.created_at', '>=', $from_date)->whereDate('packings.created_at', '<', $to_date)
                ->get();
            $result = [];
            foreach ($packings as $item) {
                $result[] = [
                    'packing' => str_pad($item->id, 8, "0", STR_PAD_LEFT),
                    'date' => $item->created_at,
                    'items_qty' => $item->items_qty,
                    'user' => $item->user_name,
                    'transfer' => $item->transfer_id,
                    'quote' => $item->quote_number
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('packing_to_transfer', $result);
        }
        return view('advancereports.packing_to_transfer', ['result' => $result]);
    }

    public function stockByProduct(Request $request)
    {
        $data = $request->all();
        $stores = Account::select('id', 'name')->where('exclude', 0)->get()->keyBy('id');
        $result = null;
        if (count($data) > 0) {
            $store = isset($data['store']) ? intval($data['store']) : null;
            $product_key = isset($data['product_key']) ? $data['product_key'] : null;
            if ($store == 0 && ($product_key == null || empty($product_key))) {
                dd('Debes ingresar el codigo del producto');
            }
            $from_date = date('Y-m-d', strtotime('-12 months'));
            $items = [];
            if ($store > 0) {
                $items = DB::table('products')->join('invoice_items', 'invoice_items.product_id', '=', 'products.id')
                    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                    ->select('accounts.name', 'products.product_key', 'products.notes', 'products.qty', 'products.price', 'products.wholesale_price', 'products.special_price', 'invoices.invoice_date')
                    ->where('products.account_id', $store)->whereDate('invoices.invoice_date', '>=', $from_date)
                    ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                    ->where('invoices.invoice_type_id', 1);
                if ($product_key != null && empty($product_key) == false) {
                    $items = $items->where('products.product_key', $product_key);
                }
                $items = $items->orderBy('invoices.id', 'DESC')->get();
            } else {
                $items = DB::table('products')->join('accounts', 'accounts.id', '=', 'products.account_id')->select('products.product_key', 'products.notes', 'products.qty', 'products.price', 'products.wholesale_price', 'products.special_price', 'accounts.name')->where('products.product_key', $product_key)->where('accounts.exclude', 0)->get();
            }
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Codigo', 'Descripcion', 'Cantidad', 'Precio Detalle', 'Precio Mayorista', 'Precio Especial', 'Tienda', 'Ultima Factura'];
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            $result = [];
            foreach ($items as $item) {
                if ($store == 0) {
                    $fields = [
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'qty' => $item->qty,
                        'price' => $item->price,
                        'wholesale_price' => $item->wholesale_price,
                        'special_price' => $item->special_price,
                        'account' => isset($item->name) ? $item->name : '',
                        'last_invoice' => isset($item->invoice_date) ? $item->invoice_date : ''
                    ];
                    fputcsv($fp, $fields, ';');
                } else {
                    if (isset($result[$item->product_key]) == false) {
                        $result[$item->product_key] = true;
                        $fields = [
                            'product_key' => $item->product_key,
                            'notes' => $item->notes,
                            'qty' => $item->qty,
                            'price' => $item->price,
                            'wholesale_price' => $item->wholesale_price,
                            'special_price' => $item->special_price,
                            'account' => isset($item->name) ? $item->name : '',
                            'last_invoice' => isset($item->invoice_date) ? $item->invoice_date : ''
                        ];
                        fputcsv($fp, $fields, ';');
                    }
                }
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('stock_by_product', $result);
        }
        return view('advancereports.stock_by_product', ['result' => $result, 'stores' => $stores]);
    }

    public function salesByTimePeriod(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $productTracking = ProductTracking::where(function ($query) {
                $query->where('original_account_id', Auth::user()->account_id)
                    ->orWhere('final_account_id', Auth::user()->account_id);
            })->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->get();
            $quantityIn = 0;
            $quantityOut = 0;
            $result = [];
            foreach ($productTracking as $item) {
                if (isset($result[$item->product_key]) == false) {
                    $result[$item->product_key] = [
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'quantityIn' => 0,
                        'quantityOut' => 0
                    ];
                }
                if ($item->original_quantity_total < 0) {
                    $result[$item->product_key]['quantityOut'] -= $item->original_quantity_total;
                }
                if ($item->final_quantity_total < 0) {
                    $result[$item->product_key]['quantityOut'] -= $item->final_quantity_total;
                }
                if ($item->original_quantity_total > 0) {
                    $result[$item->product_key]['quantityIn'] += $item->original_quantity_total;
                }
                if ($item->final_quantity_total > 0) {
                    $result[$item->product_key]['quantityIn'] += $item->final_quantity_total;
                }
            }
        }
        $products = Product::where('account_id', Auth::user()->account_id)->get();
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_time_period', $result);
        }
        return view('advancereports.sales_by_time_period', ['result' => $result]);
    }

    public function productsUnderWholesalePrice(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $result = [];
            $invoices = DB::table('invoices')->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->select('products.product_key', 'products.notes', 'invoice_items.cost', 'invoice_items.qty', 'products.wholesale_price', 'invoices.invoice_number', 'invoices.invoice_date', 'clients.name as client', 'accounts.name as account', 'invoices.employee_id')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<=', $to_date)
                ->where('invoices.price_type', 'Mayorista')
                ->where('accounts.exclude', 0)
                ->whereNull('invoice_items.deleted_at')
                ->get();
            $employees = Employee::get()->keyBy('id');
            foreach ($invoices as $item) {
                if (floatval($item->cost) >= floatval($item->wholesale_price)) {
                    continue;
                }
                $result[] = [
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'cost' => $item->cost,
                    'quantity' => $item->qty,
                    'wholesale_price' => $item->wholesale_price,
                    'invoice_number' => $item->invoice_number,
                    'invoice_date' => $item->invoice_date,
                    'client' => $item->client,
                    'employee' => isset($employees[$item->employee_id]) ? ($employees[$item->employee_id]->first_name . " " . $employees[$item->employee_id]->last_name) : '',
                    'account' => $item->account
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('products_underwholesaleprice', $result);
        }
        return view('advancereports.products_underwholesaleprice', ['result' => $result]);
    }

    public function productsUnderNormalPrice(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $result = [];
            $invoices = DB::table('invoices')->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->select('products.product_key', 'products.notes', 'invoice_items.cost', 'invoice_items.qty', 'products.normal_price', 'invoices.invoice_number', 'invoices.invoice_date', 'clients.name as client', 'accounts.name as account', 'invoices.employee_id')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<=', $to_date)
                ->where('invoices.price_type', 'Taller')
                ->where('accounts.exclude', 0)
                ->whereNull('invoice_items.deleted_at')
                ->get();
            $employees = Employee::get()->keyBy('id');
            foreach ($invoices as $item) {
                if (floatval($item->cost) >= floatval($item->normal_price)) {
                    continue;
                }
                $result[] = [
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'cost' => $item->cost,
                    'quantity' => $item->qty,
                    'normal_price' => $item->normal_price,
                    'invoice_number' => $item->invoice_number,
                    'invoice_date' => $item->invoice_date,
                    'client' => $item->client,
                    'employee' => isset($employees[$item->employee_id]) ? ($employees[$item->employee_id]->first_name . " " . $employees[$item->employee_id]->last_name) : '',
                    'account' => $item->account
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('products_undernormalprice', $result);
        }
        return view('advancereports.products_undernormalprice', ['result' => $result]);
    }

    public function clientNotes(Request $request)
    {
        $result = null;
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $tasks = Task::where('account_id', Auth::user()->account_id)->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->get();
            foreach ($tasks as $note) {
                $result[] = [
                    'client' => $note->client_name,
                    'user' => $note->user_name,
                    'description' => $note->description,
                    'created_at' => $note->created_at
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('client_notes', $result);
        }
        return view('advancereports.client_notes', ['result' => $result]);
    }

    public function tasksByEmployee(Request $request)
    {
        $result = null;
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $tasks = Task::where('client_id', '>', 0)->where('employee_id', '>', 0)->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->get();
            $employees = Employee::select('id', 'first_name', 'last_name', 'account_id', 'profile')->get()->keyBy('id');
            $accounts = Account::select('id', 'name')->get()->keyBy('id');
            $clients = Client::select('id', 'name', 'company_name', 'latitude', 'longitude', 'work_phone', 'phone')->get()->keyBy('id');
            foreach ($tasks as $task) {
                if (isset($result[$task->client_id]) == false) {
                    $invoices = Invoice::select('id', 'invoice_number')->where('client_id', $task->client_id)->whereDate('invoice_date', '>=', $from_date)->where('invoice_type_id', 1)->get()->keyBy('invoice_number');
                    $numbersArray = array_keys($invoices->toArray());
                    $invoiceNumbers = implode(',', $numbersArray);
                    $result[$task->client_id] = [
                        'client' => $clients[$task->client_id]->company_name ? $clients[$task->client_id]->company_name : $clients[$task->client_id]->name,
                        'phone' => $clients[$task->client_id]->work_phone ? $clients[$task->client_id]->work_phone : $clients[$task->client_id]->phone,
                        'employee' => $employees[$task->employee_id]->first_name . " " . $employees[$task->employee_id]->last_name,
                        'profile' => $employees[$task->employee_id]->profile,
                        'account' => $accounts[$employees[$task->employee_id]->account_id]->name,
                        'description' => $task->description,
                        'invoices' => $invoiceNumbers,
                        'pinned' => $clients[$task->client_id]->latitude ? 'Si' : 'No',
                        'created_at' => $task->created_at
                    ];
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('tasks_by_employee', $result);
        }
        return view('advancereports.tasks_by_employee', ['result' => $result]);
    }

    public function carts(Request $request)
    {
        $result = [];
        $data = $request->all();
        $accounts = Account::select('id', 'name')->get()->keyBy('id');
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $items = CartItem::whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date);
            if ($request->account_id) {
                $items->where('account_id', Auth::user()->account_id);
            }
            $items = $items->get();
            foreach ($items as $item) {
                $_item = [
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'qty' => $item->qty,
                    'account' => isset($accounts[$item->account_id]) ? $accounts[$item->account_id]->name : ''
                ];
                if (!$request->group) {
                    $result[] = $_item;
                } else {
                    if (isset($result[$item->product_key]) == false) {
                        $result[$item->product_key] = $_item;
                    } else {
                        $result[$item->product_key]['qty'] = intval($result[$item->product_key]['qty']) + intval($item->qty);
                    }
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('carts', $result);
        }
        return view('advancereports.carts', ['result' => $result, 'accounts' => $accounts]);
    }

    public function exportInventoryGeneral()
    {
        $startDate  = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();
        $products = DB::table('products')
            ->join('accounts', 'products.account_id', '=', 'accounts.id')
            ->leftJoin('brands', 'brands.brand_id', '=', 'products.brand_id')
            ->select([
                'accounts.name as account',
                'products.product_key',
                'products.notes',
                'products.qty',
                'products.cost',
                'products.price',
                'products.relation_id',
                'products.wholesale_price',
                'products.special_price',
                'products.vendor_id',
                'brands.name as brand_name',
                'products.account_id',
                'category_id',
                'sub_category_id',
                'rotation_id'
            ])
            ->where('accounts.exclude', 0)
            ->orderBy('products.account_id')->get();
        $result = Product::select('product_key', DB::raw('SUM(qty) as qty_total'))
            ->whereNotIn('account_id',  [6, 19])
            ->groupBy('product_key')->get()
            ->keyBy('product_key');
        $relatedProducts = Product::select('relation_id', DB::raw('SUM(qty) as qty_total'))
            ->whereNotNull('relation_id')
            ->whereNotIn('account_id', [6, 19])
            ->groupBy('relation_id')->get()
            ->keyBy('relation_id');
        $itemsAccount = DB::table('invoices')
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->select([
                'invoices.account_id',
                'invoice_items.product_key',
                'invoice_items.qty',
                'products.relation_id'
            ])
            ->where('invoices.invoice_type_id', 1)
            ->where('invoices.invoice_date', '>=', $startDate)
            ->where('invoices.invoice_date', '<', $endDate)
            ->whereNull('invoice_items.deleted_at')
            ->get();
        $qtySalesProducts = [];
        $qtySalesRelatedId = [];
        foreach ($itemsAccount as $item) {
            if (!isset($qtySalesProducts[$item->account_id])) {
                $qtySalesProducts[$item->account_id] = [];
            }
            if (!isset($qtySalesRelatedId[$item->account_id])) {
                $qtySalesRelatedId[$item->account_id] = [];
            }
            if (!isset($qtySalesProducts[$item->account_id][$item->product_key])) {
                $qtySalesProducts[$item->account_id][$item->product_key] = 0;
            }
            if (!isset($qtySalesRelatedId[$item->account_id][$item->relation_id])) {
                $qtySalesRelatedId[$item->account_id][$item->relation_id] = 0;
            }
            if (isset($item->product_key) && trim($item->product_key) != '') {
                $qtySalesProducts[$item->account_id][$item->product_key] += $item->qty;
            }
            if (isset($item->relation_id) && trim($item->relation_id) != '') {
                $qtySalesRelatedId[$item->account_id][$item->relation_id] += $item->qty;
            }
        }
        $qtyRelationsAccount = [];
        foreach ($products as $item) {
            if (!isset($qtyRelationsAccount[$item->account_id])) {
                $qtyRelationsAccount[$item->account_id] = [];
            }
            if (!isset($qtyRelationsAccount[$item->account_id][$item->relation_id])) {
                $qtyRelationsAccount[$item->account_id][$item->relation_id] = 0;
            }
            if (isset($item->relation_id) && trim($item->relation_id) != '') {
                $qtyRelationsAccount[$item->account_id][$item->relation_id] += $item->qty;
            }
        }
        $vendors = Vendor::get()->keyBy('id');
        $categories = Category::select('category_id', 'name')->get()->keyBy('category_id');
        $subCategories = SubCategory::select('id', 'name')->get()->keyBy('id');
        $rotations = Rotation::select('id', 'name')->get()->keyBy('id');
        $user = Auth::user()->realUser();
        $fp = fopen('inventario.csv', 'w');
        $columns = ['Codigo', 'Tienda', 'Descripcion'];
        if ($user->_can('cost')) {
            $columns[] = 'cost';
        }
        $columns = array_merge($columns, [
            'Precio Detalle',
            'Precio Mayorista',
            'Precio especial',
            'Cantidad',
            'Cantidad Global',
            'Equivalencias en Tienda',
            'Cantidad Global de Equivalencias',
            'Promedio Cantidad Vendida Ult. 6 Meses',
            'Promedio Equivalencia Vendida Ult. 6 Meses',
            'Cantidad Minima',
            'Cantidad Maxima',
            'Codigo de Equivalencia',
            'Proveedor',
            'Marca',
            'Categoria',
            'Sub Categoria',
            'Rotacion'
        ]);
        $bom = "\xEF\xBB\xBF";
        fwrite($fp, $bom);
        fputcsv($fp, $columns, ';');
        foreach ($products as $product) {
            $fields = [
                'product_key' => $product->product_key,
                'account' => $product->account,
                'notes' => $product->notes,
            ];
            if ($user->_can('cost')) {
                $fields['cost'] = $product->cost;
            }
            $fields = array_merge(
                $fields,
                [
                    'price' => $product->price,
                    'wholesale_price' => $product->wholesale_price,
                    'special_price' => $product->special_price,
                    'qty' => $product->qty,
                    'qty_global' => isset($result[$product->product_key]) ? $result[$product->product_key]->qty_total : 0,
                    'related_account_qty' => (isset($qtyRelationsAccount[$product->account_id]) && isset($qtyRelationsAccount[$product->account_id][$product->relation_id])) ? $qtyRelationsAccount[$product->account_id][$product->relation_id] : 0,
                    'qty_global_related' => isset($relatedProducts[$product->relation_id]) ? $relatedProducts[$product->relation_id]->qty_total : 0,
                    'qty_sales_products' => (isset($qtySalesProducts[$product->account_id]) && isset($qtySalesProducts[$product->account_id][$product->product_key])) ? intval(ceil($qtySalesProducts[$product->account_id][$product->product_key] / 6)) : 0,
                    'qty_sales_related' => (isset($qtySalesRelatedId[$product->account_id]) && isset($qtySalesRelatedId[$product->account_id][$product->relation_id])) ? intval(ceil($qtySalesRelatedId[$product->account_id][$product->relation_id] / 6)) : 0,
                    'min_qty' => isset($product->min_qty) ? $product->min_qty : 0,
                    'max_qty' => isset($product->max_qty) ? $product->max_qty : 0,
                    'relation_id' => isset($product->relation_id) ? $product->relation_id : '',
                    'vendor' => isset($vendors[$product->vendor_id]) ? $vendors[$product->vendor_id]->name : '',
                    'brand_name' => isset($product->brand_name) ? $product->brand_name : '',
                    'category' => isset($categories[$product->category_id]) ? $categories[$product->category_id]->name : '',
                    'sub_category' => isset($subCategories[$product->sub_category_id]) ? $subCategories[$product->sub_category_id]->name : '',
                    'rotacion' => isset($rotations[$product->rotation_id]) ? $rotations[$product->rotation_id]->name : '',
                ]
            );
            fputcsv($fp, $fields, ';');
        }
        fclose($fp);
        return redirect('/inventario.csv');
    }

    public function exportInventory()
    {
        $startDate  = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        $vendors = Vendor::all()->keyBy('id');
        $products = DB::table('products')
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.brand_id', '=', 'products.brand_id')
            ->select('products.*', 'categories.name as category_name', 'brands.name as brand_name')
            ->where('products.account_id', Auth::user()->account_id)
            ->get();
        $subCategories = SubCategory::pluck('name', 'id')->toArray();
        $rotations = Rotation::pluck('name', 'id')->toArray();
        $itemsAccount = DB::table('invoices')
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->select([
                'invoice_items.product_key',
                'invoice_items.qty',
                'products.relation_id'
            ])
            ->where('invoices.account_id', Auth::user()->account_id)
            ->where('invoices.invoice_type_id', 1)
            ->where('invoices.invoice_date', '>=', $startDate)
            ->where('invoices.invoice_date', '<', $endDate)
            ->whereNull('invoice_items.deleted_at')
            ->get();
        $qtySalesProducts = [];
        $qtySalesRelatedId = [];
        foreach ($itemsAccount as $item) {
            if (!isset($qtySalesProducts[$item->product_key])) {
                if (isset($item->product_key) && trim($item->product_key) != '') {
                    $qtySalesProducts[$item->product_key] = 0;
                }
            }
            if (!isset($qtySalesRelatedId[$item->relation_id])) {
                if (isset($item->relation_id) && trim($item->relation_id) != '') {
                    $qtySalesRelatedId[$item->relation_id] = 0;
                }
            }
            if (isset($item->product_key) && trim($item->product_key) != '') {
                $qtySalesProducts[$item->product_key] += $item->qty;
            }
            if (isset($item->relation_id) && trim($item->relation_id) != '') {
                $qtySalesRelatedId[$item->relation_id] += $item->qty;
            }
        }
        $result = Product::select('product_key', DB::raw('SUM(qty) as qty_total'))
            ->whereNotIn('account_id',  [6, 19])
            ->groupBy('product_key')->get()->keyBy('product_key');
        $date = date('Y-m-d');
        $fileName = "Inventario-${date}.csv";
        $user = Auth::user()->realUser();
        $fp = fopen($fileName, 'w');
        $columns = ['Identificador', 'Codigo', 'Descripcion'];
        if ($user->_can('cost')) {
            $columns[] = 'cost';
        }
        $columns = array_merge(
            $columns,
            [
                'Precio de venta',
                'Precio de mayorista',
                'Precio especial',
                'Precio dos',
                'Precio tres',
                'Cantidad',
                'Cantidad equivalencia',
                'Cantidad Minima',
                'Cantidad Maxima',
                'Cantidad Global',
                'Promedio Cantidad Vendida Ult. 6 Meses',
                'Promedio Equivalencia Vendida Ult. 6 Meses',
                'Marca',
                'Categoria',
                'Sub Categoria',
                'Rotacion',
                'Bodega',
                'Ubicacion',
                'Equivalencia',
                'Aplicaciones',
                'Proveedor',
                'Imagen',
                'Dias Transcurridos',
                'Fecha de Inicio',
                'Fecha de factura',
                'Fecha de inventario'
            ]
        );
        fputcsv($fp, $columns, ';');
        foreach ($products as $product) {
            $productTracking = ProductTracking::where(function ($query) {
                $query->where('original_account_id', Auth::user()->account_id)
                    ->orWhere('final_account_id', Auth::user()->account_id);
            })->where('product_key', $product->product_key)
                ->select('product_key', 'created_at')->orderBy('id', 'asc')
                ->first();
            $tracking_created_at_string = isset($productTracking) ? explode(' ', $productTracking->created_at)[0] : null;
            $tracking_created_at = !is_null($tracking_created_at_string) ? Carbon::parse($tracking_created_at_string) : null;
            $invoice_date = isset($product->invoice_date) ? $product->invoice_date : null;
            $invoice_date = !is_null($invoice_date) ? Carbon::parse($invoice_date) : null;

            $current_date = Carbon::now();

            if (!is_null($invoice_date)) {
                $date_to_compare = $invoice_date;
            } elseif (!is_null($tracking_created_at)) {
                $date_to_compare = $tracking_created_at;
            } else {
                $date_to_compare = $current_date;
            }

            $days_difference = $date_to_compare->diffInDays($current_date);


            $fields = [
                $product->public_id,
                $product->product_key,
                $product->notes,
            ];
            if ($user->_can('cost')) {
                $fields[] = $product->cost;
            }
            $fields = array_merge(
                $fields,
                [
                    $product->price,
                    $product->wholesale_price,
                    $product->special_price,
                    $product->price_two,
                    $product->price_three,
                    $product->qty,
                    $product->relation_qty,
                    $product->min_qty,
                    $product->max_qty,
                    isset($result[$product->product_key]) ? $result[$product->product_key]->qty_total : 0,
                    isset($qtySalesProducts[$product->product_key]) ? intval(ceil($qtySalesProducts[$product->product_key] / 6)) : 0,
                    isset($qtySalesRelatedId[$product->relation_id]) ? intval(ceil($qtySalesRelatedId[$product->relation_id] / 6)) : 0,
                    isset($product->brand_name) ? $product->brand_name : '',
                    $product->category_name,
                    isset($subCategories[$product->sub_category_id]) ? $subCategories[$product->sub_category_id] : '',
                    isset($rotations[$product->rotation_id]) ? $rotations[$product->rotation_id] : '',
                    $product->warehouse_name,
                    $product->location,
                    $product->relation_id,
                    $product->compatibility,
                ]
            );
            if (isset($vendors)) {
                $fields[] = $product->vendor_id ? ($vendors[$product->vendor_id] ? $vendors[$product->vendor_id]->name : '') : '';
            } else {
                $fields[] = '';
            };
            if ($product->picture) {
                $fields[] = 'Si';
            } else {
                $fields[] = 'No';
            };

            $fields = array_merge($fields, [
                $days_difference,
                isset($tracking_created_at_string) ? $tracking_created_at_string : 'N/A',
                $product->invoice_date,
                $product->stock_date,
            ]);
            fputcsv($fp, $fields, ';');
        }
        fclose($fp);
        return redirect('/' . $fileName);
    }

    public function supplies(Request $request)
    {
        $data = $request->all();
        $result = null;
        $users = User::all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $supplies = Supply::whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->orderBy('supply_date', 'DESC')->get();
            $result = [];
            foreach ($supplies as $supply) {
                $supplyComment = SupplyComment::where('supply_id', $supply->id)->orderBy('id', 'DESC')->first();
                $result[] = [
                    'supply_number' => $supply->supply_number,
                    'account' => $supply->account->name,
                    'status' => $supply->supply_status->name,
                    'quote' => $supply->quote_number,
                    'invoice' => $supply->invoice_number,
                    'packing' => $supply->packing_id,
                    'transfer' => $supply->transfer_id,
                    'request' => $supply->request_id,
                    'user' => $supply->user->first_name . " " . $supply->user->last_name,
                    'client' => $supply->client->name,
                    'phone' => $supply->client->phone,
                    'comment' => $supplyComment ? $supplyComment->comment : '',
                    'date' => $supply->supply_date,
                    'end_date' => $supply->end_date,
                    'amount' => $supply->amount,
                    'seller' => $supply->employee ? $supply->employee->first_name . " " . $supply->employee->last_name : ''
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('supplies', $result);
        }
        return view('advancereports.supplies', ['result' => $result, 'users' => $users]);
    }

    public function orders(Request $request)
    {
        $data = $request->all();
        $result = null;
        $users = User::all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $orders = Invoice::where('invoice_type_id', 4)->where('invoice_status_id', 1)->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->orderBy('invoice_date', 'DESC')->get();
            $result = [];
            foreach ($orders as $order) {
                $result[] = [
                    'order_number' => $order->invoice_number,
                    'account' => $order->account->name,
                    'user' => $order->user->first_name . " " . $order->user->last_name,
                    'client' => $order->client->name,
                    'date' => $order->invoice_date,
                    'amount' => $order->amount,
                    'seller' => $order->employee ? $order->employee->first_name . " " . $order->employee->last_name : ''
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('orders', $result);
        }
        return view('advancereports.orders', ['result' => $result, 'users' => $users]);
    }

    public function orderItems(Request $request)
    {
        $data = $request->all();
        $result = null;
        $users = User::all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $products = Product::select('id', 'product_key', 'qty')->where('account_id', 17)->get()->keyBy('product_key');
            $items = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->select('invoice_items.product_key', 'invoice_items.notes', 'invoice_items.qty', 'invoice_items.cost', 'invoices.invoice_number', 'invoices.invoice_date', 'accounts.name as account', 'users.first_name', 'users.last_name', 'clients.name as client', 'invoices.employee_id')
                ->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                ->where('invoices.invoice_type_id', 4)->where('invoice_status_id', 1)->whereDate('invoices.created_at', '>=', $from_date)->whereDate('invoices.created_at', '<=', $to_date)->orderBy('invoices.invoice_date', 'DESC')->get();
            $employees = Employee::where('is_seller', 1)->get()->keyBy('id');
            $result = [];
            foreach ($items as $item) {
                $result[] = [
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'account' => $item->account,
                    'order_number' => $item->invoice_number,
                    'user' => $item->first_name . " " . $item->last_name,
                    'employee' => isset($employees[$item->employee_id]) ? $employees[$item->employee_id]->first_name . " " . $employees[$item->employee_id]->last_name : null,
                    'client' => $item->client,
                    'date' => $item->invoice_date,
                    'qty' => $item->qty,
                    'cost' => $item->cost,
                    'qtyInWarehouse' => isset($products[$item->product_key]) ? $products[$item->product_key]->qty : 0
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('order_items', $result);
        }
        return view('advancereports.order_items', ['result' => $result, 'users' => $users]);
    }

    public function clientVisits(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $visits = DB::table('clients')->join('visits', 'visits.client_id', '=', 'clients.id')
                ->join('employees', 'employees.id', '=', 'clients.seller_id')
                ->select('clients.name as client', 'employees.first_name', 'employees.last_name', 'visits.latitude', 'visits.longitude', 'visits.created_at')
                ->whereDate('visits.created_at', '>=', $from_date)->whereDate('visits.created_at', '<=', $to_date)->get();
            $result = [];
            foreach ($visits as $visit) {
                $result[] = [
                    'client' => $visit->client,
                    'employee' => $visit->first_name . " " . $visit->last_name,
                    'latitude' => $visit->latitude,
                    'longitude' => $visit->longitude,
                    'created_at' => $visit->created_at
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('client_visits', $result);
        }
        return view('advancereports.client_visits', ['result' => $result]);
    }

    public function stockEntries(Request $request)
    {
        $_result = [];
        $data = $request->all();
        $accounts = Account::select('id', 'name')->get();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $account_id = $data['account_id'];
            $stockArray = Stock::whereDate('updated_at', '>=', $from_date)
                ->whereDate('updated_at', '<=', $to_date)
                ->where('account_id', $account_id)->get();
            foreach ($stockArray as $stock) {
                foreach ($stock->items as $item) {
                    $_result[] = [
                        'product_key' => $item->product_key,
                        'type' => $item->input ? 'Entrada' : 'Salida',
                        'notes' => $item->notes,
                        'cost_before' => $item->cost_before,
                        'cost' => $item->cost,
                        'qty_before' => $item->qty_before,
                        'qty' => $item->qty,
                        'qty_after' => $item->qty_after,
                        'price_before' => $item->price_before,
                        'price' => $item->price,
                        'wholesale_price_before' => $item->wholesale_price_before,
                        'wholesale_price' => $item->wholesale_price,
                        'special_price_before' => $item->special_price_before,
                        'special_price' => $item->special_price,
                        'comment' => $item->comment,
                        'reason' => $item->reason,
                        'created_at' => $stock->created_at,
                        'updated_at' => $stock->updated_at
                    ];
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('stock_entries', $_result);
        }
        return view('advancereports.stock_entries', ['result' => $_result, 'accounts' => $accounts]);
    }

    public function exportClients(Request $request)
    {
        $data = $request->all();
        $accounts = Account::where('accounts.exclude', 0)->select('id', 'name')->get();
        if (count($data) > 0) {
            $currendAccountId = $data['store'];
            $currentStores = ($currendAccountId == 'all') ? array_keys($accounts->keyBy('id')->toArray()) : [(int)$currendAccountId];
            $accountName = ($currendAccountId !== 'all') ? $accounts->keyBy('id')->toArray()[$currendAccountId]['name'] : 'todas_las_tiendas';
            $accountName = str_replace('-', '', $accountName);
            $accountName = str_replace('#', '', $accountName);
            $accountName = str_replace('/', '', $accountName);
            $accountName = str_replace('.', '', $accountName);
            $accountName = str_replace(',', '', $accountName);
            $accountName = str_replace(';', '', $accountName);
            $accountName = str_replace(' ', '_', $accountName);
            $columns = ['Identificador', 'Cliente', 'Telefono', 'Identidad', 'Tipo', 'Direccion', 'Vendedor', 'Puntos', 'Total Facturado', 'Ultima Factura', 'Tienda', 'Fecha Creacion',];
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'export_clients_' . $accountName . '_' . $currentDate[0] . $currentTime . '.csv';
            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = count($currentStores);
            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'export_clients';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            if ($rows == 1) {
                dispatch((new ReportExportClients($nameFile, $reportProcessId, $currentStores))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportExportClients($nameFile, $reportProcessId, $chunkStores))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'export_clients')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.export_clients', ['accounts' => $accounts, 'reportProcess' => $reportProcess]);
    }

    public function saleTransferProducts(Request $request)
    {
        $result = [];
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));

            $categories = Category::select('category_id', 'name')->get()->keyBy('category_id');
            $subCategories = SubCategory::select('id', 'name')->get()->keyBy('id');
            $rotations = Rotation::select('id', 'name')->get()->keyBy('id');

            $invoices = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select(
                    'products.product_key',
                    'products.notes',
                    'products.qty as product_qty',
                    'invoice_items.qty',
                    'invoice_items.cost',
                    'invoice_items.product_cost',
                    'products.relation_id',
                    'products.location',
                    'products.vendor_id',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.rotation_id'
                )->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<=', $to_date)
                ->where('invoices.invoice_type_id', 1)
                ->where('invoices.account_id', Auth::user()->account_id)->get();

            $transfers = DB::table('transfers')
                ->join('transfer_items', 'transfer_items.transfer_id', '=', 'transfers.id')
                ->join('products', 'products.product_key', '=', 'transfer_items.product_key')
                ->select(
                    'products.product_key',
                    'products.notes',
                    'products.qty as product_qty',
                    'products.location',
                    'products.relation_id',
                    'transfer_items.qty',
                    'products.vendor_id',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.rotation_id'
                )->whereDate('transfers.created_at', '>=', $from_date)
                ->whereDate('transfers.created_at', '<=', $to_date)
                ->where('transfers.from_account_id', Auth::user()->account_id)
                ->where('transfer_items.from_account_id', Auth::user()->account_id)
                ->where('products.account_id', Auth::user()->account_id)
                ->get();

            $productsRelatedInStore = Product::select('relation_id', DB::raw('SUM(qty) as qty'))
                ->whereNotNull('relation_id')
                ->where('account_id', Auth::user()->account_id)
                ->groupBy('relation_id')->get()->keyBy('relation_id');

            $_products = Product::select('product_key', DB::raw('SUM(qty) as qty'))
                ->whereNotIn('account_id', [6, 19])
                ->groupBy('product_key')->get()->keyBy('product_key');

            $relatedProducts = Product::select('relation_id', DB::raw('SUM(qty) as qty'))
                ->whereNotNull('relation_id')
                ->whereNotIn('account_id', [6, 19])
                ->groupBy('relation_id')->get()->keyBy('relation_id');

            $relationIds = array_keys($relatedProducts->toArray());
            $salesRelations = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('products.relation_id', DB::raw('SUM(invoice_items.qty) as total'))
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->where('accounts.exclude', 0)
                ->whereNotNull('products.relation_id')
                ->whereIn('products.relation_id', $relationIds)
                ->groupBy('products.relation_id')
                ->get();
            $salesRelations = collect($salesRelations)->keyBy('relation_id');

            $salesProducts = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('invoice_items.product_key', DB::raw('SUM(invoice_items.qty) as total'))
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->where('accounts.exclude', 0)
                ->groupBy('invoice_items.product_key')
                ->get();
            $salesProducts = collect($salesProducts)->keyBy('product_key');

            $vendors = Vendor::scope()->select('id', 'name')->get()->keyBy('id');

            $_relatedProducts = Product::select('relation_id', DB::raw('SUM(qty) as qty'))
                ->whereNotNull('relation_id')->where('account_id', 17)
                ->groupBy('relation_id')->get()->keyBy('relation_id');

            $_productsWarehouse = Product::select('product_key', DB::raw('SUM(qty) as qty'))
                ->where('account_id', 17)
                ->groupBy('product_key')->get()->keyBy('product_key');

            foreach ($invoices as $item) {
                if (!isset($result[$item->product_key])) {
                    $result[$item->product_key] = [
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'sale_qty' => 0,
                        'sale_qty_global' => isset($salesProducts[$item->product_key]) ? $salesProducts[$item->product_key]->total : 0,
                        'transfer_qty' => 0,
                        'total_qty' => 0,
                        'qty' => $item->product_qty,
                        'qty_related' => isset($productsRelatedInStore[$item->relation_id]) ? $productsRelatedInStore[$item->relation_id]->qty : 0,
                        'location' => $item->location,
                        'relation_id' => $item->relation_id,
                        'vendor' => isset($vendors[$item->vendor_id]) ? $vendors[$item->vendor_id]->name : '',
                        'relation_qty_global' => isset($relatedProducts[$item->relation_id]) ? $relatedProducts[$item->relation_id]->qty : 0,
                        'relation_qty_warehouse' => isset($_relatedProducts[$item->relation_id]) ? $_relatedProducts[$item->relation_id]->qty : 0,
                        'qty_sales_relation' => isset($salesRelations[$item->relation_id]) ? $salesRelations[$item->relation_id]->total : 0,
                        'qty_global' => isset($_products[$item->product_key]) ? $_products[$item->product_key]->qty : 0,
                        'qty_global_warehouse' => isset($_productsWarehouse[$item->product_key]) ? $_productsWarehouse[$item->product_key]->qty : 0,
                        'category_name' => isset($categories[$item->category_id]) ? $categories[$item->category_id]->name : 'N/A',
                        'sub_category_name' => isset($subCategories[$item->sub_category_id]) ? $subCategories[$item->sub_category_id]->name : 'N/A',
                        'rotacion_name' => isset($rotations[$item->rotation_id]) ? $rotations[$item->rotation_id]->name : 'N/A',
                    ];
                }
                $result[$item->product_key]['sale_qty'] += intval($item->qty);
                $result[$item->product_key]['total_qty'] += intval($item->qty);
            }
            foreach ($transfers as $item) {
                if (!isset($result[$item->product_key])) {
                    $result[$item->product_key] = [
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'sale_qty' => 0,
                        'sale_qty_global' => isset($salesProducts[$item->product_key]) ? $salesProducts[$item->product_key]->total : 0,
                        'transfer_qty' => 0,
                        'total_qty' => 0,
                        'qty' => $item->product_qty,
                        'qty_related' => isset($productsRelatedInStore[$item->relation_id]) ? $productsRelatedInStore[$item->relation_id]->qty : 0,
                        'location' => $item->location,
                        'relation_id' => $item->relation_id,
                        'vendor' => isset($vendors[$item->vendor_id]) ? $vendors[$item->vendor_id]->name : '',
                        'relation_qty_global' => isset($relatedProducts[$item->relation_id]) ? $relatedProducts[$item->relation_id]->qty : 0,
                        'relation_qty_warehouse' => isset($_relatedProducts[$item->relation_id]) ? $_relatedProducts[$item->relation_id]->qty : 0,
                        'qty_sales_relation' => isset($salesRelations[$item->relation_id]) ? $salesRelations[$item->relation_id]->total : 0,
                        'sales_qty' => 0,
                        'qty_global' => isset($_products[$item->product_key]) ? $_products[$item->product_key]->qty : 0,
                        'qty_global_warehouse' => isset($_productsWarehouse[$item->product_key]) ? $_productsWarehouse[$item->product_key]->qty : 0,
                        'category_name' => isset($categories[$item->category_id]) ? $categories[$item->category_id]->name : 'N/A',
                        'sub_category_name' => isset($subCategories[$item->sub_category_id]) ? $subCategories[$item->sub_category_id]->name : 'N/A',
                        'rotacion_name' => isset($rotations[$item->rotation_id]) ? $rotations[$item->rotation_id]->name : 'N/A',
                    ];
                }
                $result[$item->product_key]['transfer_qty'] += intval($item->qty);
                $result[$item->product_key]['total_qty'] += intval($item->qty);
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sale_transfer_products', $result);
        }
        return view('advancereports.sale_transfer_products', ['result' => $result]);
    }

    public function invoicesDraft(Request $request)
    {
        $result = [];
        $data = $request->all();
        $invoices = DB::table('invoices')->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
            ->select('invoices.invoice_number', 'invoices.invoice_date', 'accounts.name as account', 'clients.name as client', 'invoices.employee_id', 'invoices.amount')
            ->where('invoices.invoice_type_id', 1)->where('invoices.invoice_status_id', 1)
            ->where('accounts.exclude', 0)
            ->orderBy('invoices.invoice_date', 'DESC')->take(1000)->get();
        $employees = Employee::get()->keyBy('id');
        foreach ($invoices as $invoice) {
            $result[] = [
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date,
                'amount' => $invoice->amount,
                'client' => $invoice->client,
                'account' => $invoice->account,
                'employee' => isset($employees[$invoice->employee_id]) ? $employees[$invoice->employee_id]->first_name . " " . $employees[$invoice->employee_id]->last_name : ''
            ];
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('invoices_draft', $result);
        }
        return view('advancereports.invoices_draft', ['result' => $result]);
    }

    public function transfers(Request $request)
    {
        $data = $request->all();
        $accounts = Account::select('id', 'name')->get();

        if (count($data) > 0) {
            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');

            $completed = (isset($data['completed']) && $data['completed'] > 0) ? $data['completed'] : 1;

            $from_date = $data['from_date'] ? $data['from_date'] : $current_date;
            $to_date = $data['to_date'] ? $data['to_date'] : $current_date;

            $from_account_id = (isset($data['from_store']) && $data['from_store'] !== 'all') ? (int)$data['from_store'] : null;
            $to_account_id = (isset($data['to_store']) && $data['to_store'] !== 'all') ? (int)$data['to_store'] : null;

            $transfers = DB::table('transfers');
            if (!is_null($from_account_id)) {
                $transfers = $transfers->where('from_account_id', $from_account_id);
            }
            if (!is_null($to_account_id)) {
                $transfers = $transfers->where('to_account_id', $to_account_id);
            }
            $transfers = $transfers->where('complete', $completed)
                ->where(function ($query) use ($from_date, $to_date) {
                    $query->where(function ($q) use ($from_date, $to_date) {
                        $q->whereDate('created_at', '>=', $from_date)
                            ->whereDate('created_at', '<=', $to_date);
                    })
                        ->orWhere(function ($q) use ($from_date, $to_date) {
                            $q->whereDate('updated_at', '>=', $from_date)
                                ->whereDate('updated_at', '<=', $to_date);
                        })->orWhere(function ($q) use ($from_date, $to_date) {
                            $q->whereDate('accepted_date', '>=', $from_date)
                                ->whereDate('accepted_date', '<=', $to_date);
                        });
                })->get();

            $transferItems = DB::table('transfer_items')
                ->whereIn('transfer_id', collect($transfers)->pluck('id')->toArray())
                ->select('transfer_id', 'product_key', 'qty')
                ->get();

            $products = DB::table('products')->where('account_id', 17)
                ->whereIn('product_key', collect($transferItems)->pluck('product_key')->toArray())
                ->select('id', 'product_key', 'price', 'wholesale_price', 'cost', 'special_price')
                ->get();
            $transferItems = collect($transferItems)
                ->groupBy('transfer_id')
                ->map(function ($group) {
                    return $group->keyBy('product_key')->toArray();
                })->toArray();

            $products = collect($products)->keyBy('product_key');

            $_accounts = Account::pluck('name', 'id');
            $filePath = public_path('reporte_transferencias.csv');
            $fp = fopen($filePath, 'w');

            $columns = [
                "Transferencia",
                "Origen",
                "Destino",
                "Fecha de Creacion",
                "Fecha de Aceptacion",
                "Estatus",
                "Cantidad Codigos",
                "Cantidad Total",
                "Costo Total",
                "Precio Mayorista Total",
                "Precio Especial Total",
                "Precio Total",
            ];
            fputcsv($fp, $columns, ';');
            $user = Auth::user()->realUser();

            foreach ($transfers as $transfer) {
                $_transfer = [
                    'id' => $transfer->id,
                    'from_account' => $_accounts[$transfer->from_account_id],
                    'to_account' => $_accounts[$transfer->to_account_id],
                    'created_at' => $transfer->created_at,
                    'accepted_date' => isset($transfer->accepted_date) ? $transfer->accepted_date : 'N/A',
                    'status' => ($transfer->complete == 1) ? 'Completada' : (($transfer->complete == 2) ? 'Pendiente' : 'Borrador'),
                ];
                $_transfer['qty_products'] = 0;
                $_transfer['qty_total'] = 0;
                $_transfer['sales_cost'] = 0;
                $_transfer['sales_wholesale_price'] = 0;
                $_transfer['sales_special_price'] = 0;
                $_transfer['sales_price'] = 0;

                $items = isset($transferItems[$transfer->id]) ? $transferItems[$transfer->id] : [];

                foreach ($items as $item) {
                    $_transfer['qty_total'] += $item->qty;
                    $_transfer['qty_products'] += 1;

                    if (isset($products[$item->product_key])) {
                        $product = $products[$item->product_key];
                        if ($user->_can('cost')) {
                            $_transfer['sales_cost'] += $product->cost * $item->qty;
                        }
                        $_transfer['sales_wholesale_price'] += $product->wholesale_price * $item->qty;
                        $_transfer['sales_special_price'] += $product->special_price * $item->qty;
                        $_transfer['sales_price'] += $product->price * $item->qty;
                    } else {
                        $product = DB::table('products')->where('product_key', $item->product_key)->where('account_id', $transfer->to_account_id)->orWhere('account_id', $transfer->from_account_id)->first();
                        if ($user->_can('cost')) {
                            $_transfer['sales_cost'] += $product->cost * $item->qty;
                        }
                        $_transfer['sales_wholesale_price'] += $product->wholesale_price * $item->qty;
                        $_transfer['sales_special_price'] += $product->special_price * $item->qty;
                        $_transfer['sales_price'] += $product->price * $item->qty;
                    }
                }
                fputcsv($fp, $_transfer, ';');
            }
            fclose($fp);
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        return view('advancereports.transfers', ['stores' => $accounts]);
    }

    public function transferItemsPending(Request $request)
    {
        $result = [];
        $transfers = Transfer::with('items')->where('complete', 2)->take(1000)->get();
        $accounts = Account::select('id', 'name')->get()->keyBy('id');

        foreach ($transfers as $transfer) {
            foreach ($transfer->items as $item) {
                if ($item->complete == 0) {
                    if (isset($item->product_id) && $item->product_id != 0) {
                        $product = Product::with('vendor')->withTrashed()->find($item->product_id);
                    } else if (isset($item->from_account_id) && isset($item->product_key)) {
                        $product = Product::with('vendor')->withTrashed()->where('account_id', $item->from_account_id)->where('product_key', $item->product_key)->first();
                    } else {
                        $product = Product::with('vendor')->withTrashed()->where('product_key', $item->product_key)->first();
                    }
                    $result[] = [
                        'vendor' => isset($product->vendor->name) ? $product->vendor->name : '',
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'qty' => $item->qty,
                        'created_at' => $transfer->created_at,
                        'transfer' => $transfer->id,
                        'from_account' => $accounts[$transfer->from_account_id]->name,
                        'to_account' => $accounts[$transfer->to_account_id]->name,
                    ];
                }
            }
        }
        if ($request->export) {
            return $this->export('transfer_items_pending', $result);
        }
        return view('advancereports.transfer_items_pending', ['result' => $result]);
    }

    public function transferItemsAccepted(Request $request)
    {
        // transfer_items_accepted
        $stores = Account::select('id', 'name')->get();
        $data = $request->all();
        $from_date = isset($data['from_date']) ? date('Y-m-d', strtotime($data['from_date'])) : date('Y-m-d');
        $to_date = isset($data['to_date']) ? date('Y-m-d', strtotime($data['to_date'])) : date('Y-m-d');
        if (count($data) <= 0) {
            return view('advancereports.transfer_items_accepted', ['stores' => $stores]);
        }
        $result = [];
        $from_account = (isset($data['from_account']) && intval($data['from_account']) > 0) ? $data['from_account'] : null;
        $to_account = (isset($data['to_account']) && intval($data['to_account']) > 0) ? $data['to_account'] : null;
        $transfers = Transfer::with('items')
            ->whereDate('created_at', '>=', $from_date)
            ->whereDate('created_at', '<=', $to_date)
            ->when(!is_null($from_account), function ($query) use ($from_account) {
                return $query->where('from_account_id', $from_account);
            })
            ->when(!is_null($to_account), function ($query) use ($to_account) {
                return $query->where('to_account_id', $to_account);
            })
            ->where('complete', '>', 0)->get();
        // dd($transfers);
        $accounts = Account::select('id', 'name')->get()->keyBy('id');
        foreach ($transfers as $transfer) {
            foreach ($transfer->items as $item) {
                if ($item->complete == 1) {
                    if (isset($item->product_id) && $item->product_id != 0) {
                        $product = Product::with('vendor')->withTrashed()->find($item->product_id);
                    } else if (isset($item->from_account_id) && isset($item->product_key)) {
                        $product = Product::with('vendor')->withTrashed()->where('account_id', $item->from_account_id)->where('product_key', $item->product_key)->first();
                    } else {
                        $product = Product::with('vendor')->withTrashed()->where('product_key', $item->product_key)->first();
                    }
                    // dd($item);
                    $result[] = [
                        'vendor' => isset($product->vendor->name) ? $product->vendor->name : '',
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'qty' => $item->qty,
                        'created_at' => $transfer->created_at,
                        'transfer' => $transfer->id,
                        'from_account' => $accounts[$transfer->from_account_id]->name,
                        'to_account' => $accounts[$transfer->to_account_id]->name,
                    ];
                }
            }
        }
        if ($data['export'] == 0) {
            return view('advancereports.transfer_items_accepted', ['stores' => $stores, 'result' => $result]);
        } else {
            return $this->export('transfer_items_pending', $result);
        };
    }

    public function importsPending(Request $request)
    {
        $result = [];
        $data = $request->all();
        $imports = ImportTracking::where('complete', 0)->take(100)->get();
        $accounts = Account::all();
        $_accounts = [];
        foreach ($accounts as $account) {
            $_accounts[$account->id] = $account->name;
        }
        foreach ($imports as $import) {
            $import->account = $_accounts[$import->account_id];
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('imports_pending', $imports);
        }
        return view('advancereports.imports_pending', ['result' => $imports]);
    }

    public function transferItemsRemain(Request $request)
    {
        $result = [];
        $data = $request->all();
        $transferItems = TransferItem::where('qty_returned', '<>', 0)->take(1000)->get();
        $accounts = Account::all();
        $_accounts = [];
        foreach ($accounts as $account) {
            $_accounts[$account->id] = $account->name;
        }
        foreach ($transferItems as $transferItem) {
            $transferItem->from_account = $_accounts[$transferItem->from_account_id];
            $transferItem->to_account = $_accounts[$transferItem->to_account_id];
        }
        if (isset($data['export']) && $data['export'] == 1) {
            $filePath = public_path('reportes.csv');
            $fp = fopen($filePath, 'w');
            if ($fp === false) {
                return '/settings/reports';
            }
            $columns = [
                "Codigo",
                "Producto",
                "Transferencia",
                "Fecha",
                "Origen",
                "Destino",
                "Cantidad Enviada",
                "Cantidad Recibida",
                "Sobrante/Faltante",
                "Comentario"
            ];
            fputcsv($fp, $columns, ';');
            foreach ($transferItems as $transferItem) {
                $rowData = [
                    $transferItem->product_key,
                    $transferItem->notes,
                    $transferItem->transfer_id,
                    $transferItem->created_at,
                    $transferItem->from_account,
                    $transferItem->to_account,
                    $transferItem->qty_sent,
                    $transferItem->qty_received,
                    $transferItem->qty_returned,
                    $transferItem->description,
                ];
                fputcsv($fp, $rowData, ';');
            }
            fclose($fp);
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        return view('advancereports.transferitems_remain', ['result' => $transferItems]);
    }

    public function mostSelledProducts(Request $request)
    {
        $result = [];
        $data = $request->all();
        $accounts = Account::select('id', 'name')->get()->keyBy('id');
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $categories = Category::select('category_id', 'name')->get()->keyBy('category_id');
            $subCategories = SubCategory::select('id', 'name')->get()->keyBy('id');
            $rotations = Rotation::select('id', 'name')->get()->keyBy('id');

            $account_id = isset($data['account_id']) ? $data['account_id'] : null;
            $items = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select(
                    'invoice_items.product_id',
                    'invoice_items.product_key',
                    'invoice_items.notes',
                    DB::raw('SUM(invoice_items.qty) as qty'),
                    DB::raw('SUM(invoice_items.qty * invoice_items.product_cost) as total_cost'),
                    DB::raw('SUM(invoice_items.qty * invoice_items.cost) as total'),
                    'invoices.account_id',
                    'products.qty as product_qty',
                    'products.relation_id',
                    'products.vendor_id',
                    'invoice_items.product_cost'
                )->where('accounts.exclude', 0)->whereNull('invoice_items.deleted_at')
                ->whereDate('invoices.invoice_date', '>=', $from_date)->whereDate('invoices.invoice_date', '<=', $to_date)
                ->where('invoices.invoice_type_id', 1);
            if ($account_id > 0) {
                $items = $items->where('invoices.account_id', $account_id);
            }
            $items = $items->groupBy('invoice_items.product_key')->orderBy('qty', 'DESC')->take(500)->get();

            $productsTotal = Product::select('product_key', DB::raw('SUM(qty) as qty_total'))
                ->whereNotIn('account_id',  [6, 19])
                ->groupBy('product_key')->get()->keyBy('product_key');

            $productsInAccount = Product::select('product_key', DB::raw('SUM(qty) as qty_total'))
                ->whereNotIn('account_id',  [6, 19]);
            if ($account_id > 0) {
                $productsInAccount = $productsInAccount->where('account_id', $account_id);
            }
            $productsInAccount = $productsInAccount->groupBy('product_key')->get()->keyBy('product_key');

            $relatedInAccount = Product::select('relation_id', DB::raw('SUM(qty) as qty_total'))
                ->whereNotIn('account_id',  [6, 19]);
            if ($account_id > 0) {
                $relatedInAccount = $relatedInAccount->where('account_id', $account_id);
            }
            $relatedInAccount = $relatedInAccount->groupBy('relation_id')->get()->keyBy('relation_id');

            $vendors = Vendor::get()->keyBy('id');
            $products = Product::select(
                'id',
                'product_key',
                'qty',
                'category_id',
                'sub_category_id',
                'rotation_id'
            )->where('account_id', 17)->get()->keyBy('product_key');
            $relationIdQty = Product::select('relation_id', DB::raw('SUM(qty) as qty'))
                ->where('account_id', 17)->groupBy('relation_id')->get()->keyBy('relation_id');
            foreach ($items as $item) {
                if ($item->qty < 50) {
                    continue;
                }
                if (!isset($result[$item->product_key])) {
                    $categoryId = isset($products[$item->product_key]) ? $products[$item->product_key]->category_id : null;
                    $subCategoryId = isset($products[$item->product_key]) ? $products[$item->product_key]->sub_category_id : null;
                    $rotationId = isset($products[$item->product_key]) ? $products[$item->product_key]->rotation_id : null;
                    $result[$item->product_key] = [
                        'product_key' => $item->product_key,
                        'notes' => $item->notes,
                        'qty' => $item->qty,
                        'cost' => $item->product_cost,
                        'total' => $item->total,
                        'total_cost' => $item->total_cost,
                        'relationQtyInWarehouse' => isset($relationIdQty[$item->relation_id]) ? $relationIdQty[$item->relation_id]->qty : '',
                        'relationQtyInStore' => isset($relatedInAccount[$item->relation_id]) ? $relatedInAccount[$item->relation_id]->qty_total : 0,
                        'qtyInStore' => isset($productsInAccount[$item->product_key]) ? $productsInAccount[$item->product_key]->qty_total : 0,
                        'qtyInWarehouse' => isset($products[$item->product_key]) ? $products[$item->product_key]->qty : '',
                        'relation_id' => $item->relation_id,
                        'vendor' => isset($vendors[$item->vendor_id]) ? $vendors[$item->vendor_id]->name : '',
                        'qty_global' => isset($productsTotal[$item->product_key]) ? $productsTotal[$item->product_key]->qty_total : 0,
                        'category_name' => (isset($categoryId) && isset($categories[$categoryId])) ? $categories[$categoryId]->name : 'N/A',
                        'sub_category_name' => (isset($subCategoryId) && isset($subCategories[$subCategoryId])) ? $subCategories[$subCategoryId]->name : 'N/A',
                        'rotacion_name' => (isset($rotationId) && isset($rotations[$rotationId])) ? $rotations[$rotationId]->name : 'N/A',
                    ];
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('most_selled_products', $result);
        }
        return view('advancereports.most_selled_products', ['result' => $result, 'accounts' => $accounts]);
    }

    public function invoicePoints(Request $request)
    {
        $result = null;
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $invoices = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('invoices.invoice_number', 'invoices.invoice_date', 'invoices.amount', 'invoices.discount_points', 'invoices.discount', 'clients.name as client', 'accounts.name as account')
                ->where('accounts.exclude', 0)->where('invoice_type_id', 1)->where('discount', '>', 0)
                ->where('invoices.invoice_date', '>', $from_date)->where('invoices.invoice_date', '<', $to_date)->get();
            $result = $invoices;
            if (isset($data['export']) && $data['export'] == 1) {
                return $this->export('invoice_points', $result);
            }
        }
        return view('advancereports.invoice_points', ['result' => $result]);
    }

    public function customersPoints(Request $request)
    {
        $clients = Client::select('name', 'company_name', 'invoice_date', 'phone', 'work_phone', 'id', 'seller_id', 'address1', 'points', 'account_id')->where('points', '>', 0)->get();
        $employees = Employee::select('first_name', 'last_name', 'id', 'profile')->get()->keyBy('id');
        $accounts = Account::select('id', 'name')->where('exclude', 0)->get()->keyBy('id');
        $result = [];
        if ($request->method() == 'POST') {
            $data = $request->all();
            foreach ($clients as $client) {
                if (isset($result[$client->id]) == false) {
                    $result[$client->id] = [
                        'id' => $client->id,
                        'name' => $client->company_name ? $client->company_name : $client->name,
                        'phone' =>  $client->work_phone ? $client->work_phone : $client->phone,
                        'points' => $client->points,
                        'address' => $client->address1,
                        'invoice_date' => $client->invoice_date,
                        'employee' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id]->first_name . " " . $employees[$client->seller_id]->last_name : '',
                        'profile' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id]->profile : '',
                        'account' => isset($accounts[$client->account_id]) ? $accounts[$client->account_id]->name : '',
                    ];
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('customers_points', $result);
        }
        return view('advancereports.customers_points', ['result' => $result]);
    }

    public function customerPurchasesFrequency(Request $request)
    {
        $result = null;
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $clients = Client::where('account_id', Auth::user()->account_id)->get();
            $employees = Employee::select('id', 'first_name', 'last_name')->get()->keyBy('id');
            foreach ($clients as $client) {
                if (isset($result[$client->id]) == false) {
                    $result[$client->id] = [
                        'id' => $client->id,
                        'name' => $client->company_name,
                        'firstname' => $client->name,
                        'lastname' => $client->name,
                        'phone' =>  $client->work_phone,
                        'type' => $client->type,
                        'employee' => isset($employees[$client->seller_id]) ? $employees[$client->seller_id]->first_name . " " . $employees[$client->seller_id]->last_name : '',
                        'points' => $client->points,
                        'invoicesQtyTotal' => count($client->invoices),
                        'invoicesQty' => 0,
                        'invoicesRevenue' => 0,
                        'invoicesRevenueTotal' => 0,
                        'lastPurchaseDate' => ''
                    ];
                    foreach ($client->invoices as $invoice) {
                        if (strtotime($invoice->invoice_date) >= strtotime($from_date) && strtotime($invoice->invoice_date) <= strtotime($to_date)) {
                            $result[$client->id]['invoicesQty'] += 1;
                            $result[$client->id]['invoicesRevenue'] +=  floatval($invoice->amount);
                        }
                        $result[$client->id]['invoicesQtyTotal'] += 1;
                        $result[$client->id]['invoicesRevenueTotal'] +=  floatval($invoice->amount);
                        $result[$client->id]['lastPurchaseDate'] = $invoice->invoice_date;
                    }
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('customer_purchases_frequency', $result);
        }
        return view('advancereports.customer_purchases_frequency', ['result' => $result]);
    }

    public function inputEntries(Request $request)
    {
        $data = $request->all();
        $accounts = Account::where('accounts.exclude', 0)->select('id', 'name')->get();
        if (count($data) > 0) {
            $currendAccountId = $data['store'];
            $currentStores = ($currendAccountId == 'all') ? array_keys($accounts->keyBy('id')->toArray()) : [(int)$currendAccountId];
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $columns = [
                'Fecha',
                'Tienda',
                'Proveedor',
                'Estado',
                'Tipo',
                'Codigo',
                'Producto',
                'Cantidad Anterior',
                'Cantidad Actualizada',
                'Cantidad Final',
                'Cantidad Bodega',
                'Cantidad Tienda',
                'Precio Vendido Al Cliente',
                'Costo Anterior',
                'Costo Final',
                'Precio Anterior',
                'Precio Final',
                'Precio Mayorista Anterior',
                'Precio Mayorista Final',
                'Precio Especial Anterior',
                'Precio Especial Final',
                'Razon',
                'Comentario'
            ];
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'input_entries_' . $currentDate[0] . $currentTime . '.csv';
            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = count($currentStores);
            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'input_entries';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            if ($rows == 1) {
                dispatch((new ReportInputEntries($nameFile, $reportProcessId, $currentStores, $from_date, $to_date))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportInputEntries($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'input_entries')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.input_entries', ['accounts' => $accounts, 'reportProcess' => $reportProcess]);
    }

    public function exportInvoices(Request $request)
    {
        $data = $request->all();
        $accounts = Account::where('accounts.exclude', 0)->select('id', 'name')->get();
        if (count($data) > 0) {
            $currentAccountId = $data['store'];
            $filter = $data['filter'];
            $currentStores = ($currentAccountId == 'all') ? array_keys($accounts->keyBy('id')->toArray()) : [(int)$currentAccountId];
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $columns = ['invoice_id', 'company', 'account', 'client', 'RTN', 'invoice_number', 'cai', 'invoice_date', 'total'];
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'export_invoices_' . $currentDate[0] . $currentTime . '.csv';
            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = count($currentStores);
            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'export_invoices';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            if ($rows == 1) {
                dispatch((new ReportExportInvoices($nameFile, $reportProcessId, $currentStores, $from_date, $to_date, $filter))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportExportInvoices($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date, $filter))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'export_invoices')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.export_invoices', ['accounts' => $accounts, 'reportProcess' => $reportProcess]);
    }

    public function exportInventoryNotLocations()
    {
        $vendors = Vendor::all()->keyBy('id');
        $products = DB::table('products')
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->leftJoin('brands', 'brands.brand_id', '=', 'products.brand_id')
            ->select('products.*', 'categories.name as category_name', 'brands.name as brand_name')
            ->where('products.account_id', Auth::user()->account_id)
            ->where('products.qty', '>', 0)
            ->where(function ($query) {
                $query->whereIn('products.location', ['N/A', 'null', '', ' '])
                    ->orWhereNull('products.location');
            })
            ->get();
        $result = Product::select('product_key', DB::raw('SUM(qty) as qty_total'))
            ->whereIn('id', collect($products)->pluck('id')->toArray())
            ->whereNotIn('account_id',  [6, 19])
            ->groupBy('product_key')
            ->get()->keyBy('product_key');
        foreach ($products as $product) {
            $product->qty_global = isset($result[$product->product_key]) ? $result[$product->product_key]->qty_total : 0;
        }
        $date = date('Y-m-d');
        $fileName = "Inventario-sin-ubicacion-" . date('Y_m_d_H_i') . ".csv";
        $user = Auth::user()->realUser();
        $fp = fopen($fileName, 'w');
        $columns = ['Identificador', 'Codigo', 'Descripción',];
        if ($user->_can('cost')) {
            $columns[] = 'cost';
        }
        $columns = array_merge($columns, ['Precio de venta', 'Precio de mayorista', 'Precio especial', 'Precio dos', 'Precio tres', 'Cantidad', 'Categoria', 'Marca', 'Bodega', 'Ubicacion', 'Equivalencia', 'Cantidad equivalencia', 'Aplicaciones', 'Proveedor', 'Imagen', 'Fecha de factura', 'Fecha de inventario', 'Cantidad Global']);
        fputcsv($fp, CSV_SEPARATOR, ';');
        fputcsv($fp, $columns, ';');
        foreach ($products as $product) {
            $fields = [
                $product->public_id,
                $product->product_key,
                $product->notes,
            ];
            if ($user->_can('cost')) {
                $fields[] = $product->cost;
            }
            $fields = array_merge($fields, [
                $product->price,
                $product->wholesale_price,
                $product->special_price,
                $product->price_two,
                $product->price_three,
                $product->qty,
                $product->category_name,
                isset($product->brand_name) ? $product->brand_name : '',
                $product->warehouse_name,
                $product->location,
                $product->relation_id,
                $product->relation_qty,
                $product->compatibility,
            ]);
            if (isset($vendors)) {
                $fields[] = $product->vendor_id ? ($vendors[$product->vendor_id] ? $vendors[$product->vendor_id]->name : '') : '';
            } else {
                $fields[] = '';
            };
            if ($product->picture) {
                $fields[] = 'Si';
            } else {
                $fields[] = 'No';
            };

            $fields = array_merge($fields, [
                $product->invoice_date,
                $product->stock_date,
                $product->qty_global ? $product->qty_global : 0,
            ]);

            fputcsv($fp, $fields, ';');
        }
        fclose($fp);
        return redirect('/' . $fileName);
    }

    public function refunds(Request $request)
    {
        $_result = [];
        $accounts = Account::select('id', 'name')->get()->keyBy('id');
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $refunds = Refund::with(['refund_items', 'account', 'employee', 'invoice'])->whereDate('refund_date', '>=', $from_date)
                ->whereDate('refund_date', '<=', $to_date)
                ->get();
            $products = Product::select('product_key', 'cost')->get()->keyBy('product_key');
            foreach ($refunds as $refund) {
                foreach ($refund->refund_items as $item) {
                    $cost = isset($products[$item->product_key]) ? floatval($products[$item->product_key]->cost) : 0;
                    $employee = $refund->employee ? $refund->employee->first_name . " " . $refund->employee->last_name : '';
                    if (empty($refund->invoice)) {
                        $_result[] =  [
                            'product_key' => $item->product_key,
                            'invoice_number' => "N/A",
                            'notes' => $item->notes,
                            'cost' => $item->cost,
                            'product_cost' => $cost,
                            'qty' => $item->qty,
                            'employee' => $employee,
                            'account' => $refund->account->name,
                            'total' => $item->cost * $item->qty,
                            'total_cost' => $cost * $item->qty,
                            'total_invoice' => "N/A",
                            'date' => $refund->refund_date,
                            'return_reason' => $item->return_reason,
                            'tipo' => $item->return_money ? 'Efectivo' : 'Credito Tienda',
                            'cause' => $item->return_stock ? 'Reinventariado' : 'Producto dañado',
                            'vendor' => $item->product->vendor->name ?? '',
                            'client' => $item->refund->client->name ?? '',
                            'phone' => $item->refund->client->phone ?? '',
                        ];
                    } else {
                        $_result[] =  [
                            'product_key' => $item->product_key,
                            'invoice_number' => $refund->invoice->invoice_number,
                            'notes' => $item->notes,
                            'cost' => $item->cost,
                            'product_cost' => $cost,
                            'qty' => $item->qty,
                            'employee' => $employee,
                            'account' => $refund->account->name,
                            'total' => $item->cost * $item->qty,
                            'total_cost' => $cost * $item->qty,
                            'total_invoice' => $refund->invoice->total,
                            'date' => $refund->refund_date,
                            'return_reason' => $item->return_reason,
                            'tipo' => $item->return_money ? 'Efectivo' : 'Credito Tienda',
                            'cause' => $item->return_stock ? 'Reinventariado' : 'Producto dañado',
                            'vendor' => $item->product->vendor->name ?? '',
                            'client' => $item->refund->client->name ?? '',
                            'phone' => $item->refund->client->phone ?? '',
                        ];
                    }
                }
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('refunds', $_result);
        }
        return view('advancereports.refunds', ['result' => $_result, 'accounts' => $accounts]);
    }

    public function invoicesConverted(Request $request)
    {
        $result = [];
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $invoices = Invoice::with(['invoice_items', 'account'])
                ->whereDate('invoice_date', '>=', $from_date)
                ->whereDate('invoice_date', '<=', $to_date)
                ->whereNotNull('quote_id')
                ->whereNotNull('quote_number')
                ->get();
            $quotes = InvoiceHistory::whereDate('invoice_date', '>=', $from_date)
                ->whereDate('invoice_date', '<=', $to_date)
                ->whereIn('invoice_type_id', [3, 2])
                ->get()->keyBy('invoice_id');
            $fp = fopen('inventario.csv', 'w');
            $columns = ['Factura', 'Codigo',  'Descripcion', 'Cantidad Original', 'Precio Original', 'Subtotal Original', 'Cantidad Final', 'Precio Final', 'Subtotal Final', 'Fecha', 'Tienda'];
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($invoices as $invoice) {
                $quote = isset($quotes[$invoice->id]) ? $quotes[$invoice->id] : null;
                if (!$quote) {
                    continue;
                }
                if ($invoice->amount == $quote->amount) {
                    continue;
                }
                $post = json_decode($quote->post_data, true);
                $quoteItems = $post['invoice_items'];
                foreach ($invoice->invoice_items as $invoiceItem) {
                    $result = [
                        'invoice_number' => $invoice->invoice_number,
                        'product_key' => $invoiceItem->product_key,
                        'notes' => $invoiceItem->notes,
                        'original_qty' => isset($quoteItems[$invoiceItem->product_key]) ? $quoteItems[$invoiceItem->product_key]['qty'] : 0,
                        'original_cost' => isset($quoteItems[$invoiceItem->product_key]) ? $quoteItems[$invoiceItem->product_key]['cost'] : 0,
                        'original_total' => isset($quoteItems[$invoiceItem->product_key]) ? $quoteItems[$invoiceItem->product_key]['qty'] * $quoteItems[$invoiceItem->product_key]['cost'] : 0,
                        'final_qty' => $invoiceItem->qty,
                        'final_cost' => $invoiceItem->cost,
                        'final_total' => $invoiceItem->qty * $invoiceItem->cost,
                        'invoice_date' => $invoice->invoice_date,
                        'account' => $invoice->account->name
                    ];
                    fputcsv($fp, $result, ';');
                    if (isset($quoteItems[$invoiceItem->product_key])) {
                        unset($quoteItems[$invoiceItem->product_key]);
                    }
                }
                if (is_array($quoteItems)) {
                    foreach ($quoteItems as $item) {
                        $result = [
                            'invoice_number' => $invoice->invoice_number,
                            'product_key' => $item['product_key'],
                            'notes' => $item['notes'],
                            'original_qty' => $item['qty'],
                            'original_cost' => $item['cost'],
                            'original_total' => $item['qty'] * $item['cost'],
                            'final_qty' => 0,
                            'final_cost' => 0,
                            'final_total' => 0,
                            'invoice_date' => $invoice->invoice_date,
                            'account' => $invoice->account->name
                        ];
                        fputcsv($fp, $result, ',');
                    }
                }
            }
            fclose($fp);
            return redirect('/inventario.csv');
        }
        return view('advancereports.invoices_converted', ['result' => $result]);
    }

    public function mostRequestedProducts(Request $request)
    {
        $result = [];
        $data = $request->all();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $items = ProductRequest::with('product')->where('confirmed', null)->whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<', $to_date)->get();
            $relatedProducts = Product::select('relation_id', DB::raw('SUM(qty) as qty'))->whereNotNull('relation_id')->whereNotIn('account_id', [6, 19])->groupBy('relation_id')->get()->keyBy('relation_id');
            $_relatedProducts = Product::select('relation_id', DB::raw('SUM(qty) as qty'))->whereNotNull('relation_id')->where('account_id', 17)->groupBy('relation_id')->get()->keyBy('relation_id');
            $vendors = Vendor::scope()->select('id', 'name')->get()->keyBy('id');
            $_products = Product::select('product_key', DB::raw('SUM(qty) as qty'))->whereNotIn('account_id', [6, 19])->groupBy('product_key')->get()->keyBy('product_key');
            $sales = DB::table('invoices')->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('products.product_key', DB::raw('SUM(invoice_items.qty) as total'))
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->where('accounts.exclude', 0)
                ->groupBy('products.product_key')
                ->get();
            $sales = collect($sales)->keyBy('product_key');
            $result = [];
            foreach ($items as $item) {
                if (isset($result[$item->product_key]) == false) {
                    $_product = Product::where('account_id', 17)->where('product_key', $item->product_key)->first();
                    $product = $item->product;
                    $result[$item->product_key] = [
                        'product_key' => $item->product_key,
                        'notes' => $item->description,
                        'quantity' => 0,
                        'available' => $_product ? $_product->qty : 0,
                        'count' => 0,
                        'accounts' => [],
                        'vendor' => isset($vendors[$product->vendor_id]) ? $vendors[$product->vendor_id]->name : '',
                        'created_at' => $item->created_at,
                        'relation_id' => $_product ? $_product->relation_id : '',
                        'relation_qty_global' => isset($relatedProducts[$product->relation_id]) ? $relatedProducts[$product->relation_id]->qty : 0,
                        'relation_qty_warehouse' => isset($_relatedProducts[$product->relation_id]) ? $_relatedProducts[$product->relation_id]->qty : 0,
                        'qty_sales' => isset($sales[$product->product_key]) ? $sales[$product->product_key]->total : 0,
                        'qty_global' => isset($_products[$product->product_key]) ? $_products[$product->product_key]->qty : 0
                    ];
                }
                $result[$item->product_key]['quantity'] += $item->qty;
                $result[$item->product_key]['count'] += 1;
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('most_requested_products', $result);
        }
        return view('advancereports.most_requested_products', ['result' => $result]);
    }

    protected function lessSelledProducts(Request $request)
    {
        $_result = [];
        $data = $request->all();
        $accounts = Account::select('id', 'name')->get()->keyBy('id');
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $account_id = isset($data['account_id']) ? $data['account_id'] : null;
            $items = DB::table('invoices')->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('invoice_items.product_key', 'invoice_items.notes', DB::raw('SUM(invoice_items.qty) as qty'), DB::raw('SUM(invoice_items.qty * invoice_items.cost) as amount'))
                ->where('accounts.exclude', 0)
                ->where('invoices.invoice_type_id', 1)->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date);
            if ($account_id > 0) {
                $items = $items->where('invoices.account_id', $account_id);
            }
            $items = $items->groupBy('invoice_items.product_key')->orderBy('invoice_items.qty', 'ASC')->take(100)->get();
            foreach ($items as $key => $item) {
                $_product = Product::where('account_id', Auth::user()->account_id)->where('product_key', $item->product_key)->first();
                $qtyInStore = $_product ? $_product->qty : 0;
                $_product = Product::where('account_id', 17)->where('product_key', $item->product_key)->first();
                $qtyInWarehouse = $_product ? $_product->qty : 0;
                $_result[] = [
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'qty' => round($item->qty, 2),
                    'qtyInStore' => $qtyInStore,
                    'qtyInWarehouse' => $qtyInWarehouse,
                    'amount' => $item->amount,
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('less_selled_products', $_result);
        }
        return view('advancereports.less_selled_products', ['result' => $_result, 'accounts' => $accounts]);
    }

    protected function unselledProducts(Request $request)
    {
        $_result = [];
        $accounts = Account::select('id', 'name')->get()->keyBy('id');
        $data = $request->all();
        if (count($data) > 0) {
            $month_ago = $data['month_ago'];
            $account_id = $data['account_id'];
            $products = Product::select('id', 'product_key', 'notes', 'qty', 'cost', 'price', 'wholesale_price', 'special_price');
            if ($account_id) {
                $products->where('account_id', $account_id);
            }
            $products = $products->get()->keyBy('id');

            $from_date = date('Y-m-d', strtotime(date('Y-m-d') . " - $month_ago months"));
            $items = DB::table('invoices')->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select('invoice_items.product_key', 'invoice_items.notes', 'invoice_items.account_id', 'invoice_items.product_id')
                ->where('accounts.exclude', 0)
                ->where('invoices.invoice_type_id', 1)->whereDate('invoices.invoice_date', '>=', $from_date);
            if ($account_id) {
                $items->where('invoices.account_id', $account_id);
            }
            $items = $items->get();
            foreach ($items as $key => $item) {
                unset($products[$item->product_id]);
            }
            foreach ($products as $product) {
                $_result[] =  [
                    'product_key' => $product->product_key,
                    'notes' => $product->notes,
                    'cost' => $product->cost,
                    'price' => $product->price,
                    'wholesale_price' => $product->wholesale_price,
                    'special_price' => $product->special_price,
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('unselled_products', $_result);
        }
        return view('advancereports.unselled_products', ['result' => $_result, 'accounts' => $accounts]);
    }

    public function availableCash(Request $request)
    {
        $financeAccounts = FinanceAccount::with('account')->where('name', 'LIKE', '%EFECTIVO%')->orWhere('name', 'LIKE', '%CHICA%')->orderBy('account_id')->get();
        $result = [];
        foreach ($financeAccounts as $item) {
            if (isset($result[$item->account_id]) == false) {
                $result[$item->account_id] = [
                    'account' => $item->account->name,
                    'sales_cash' => 0,
                    'petty_cash' => 0,
                    'total' => 0
                ];
            }
            if (strpos($item->name, 'EFECTIVO') !== false) {
                $result[$item->account_id]['sales_cash'] = floatval($item->amount);
            }
            if (strpos($item->name, 'CHICA') !== false) {
                $result[$item->account_id]['petty_cash'] = floatval($item->amount);
            }
            $result[$item->account_id]['total'] = $result[$item->account_id]['sales_cash'] + $result[$item->account_id]['petty_cash'];
        }
        if ($request->export) {
            return $this->export('available_cash', $result);
        }
        return view('advancereports.available_cash', ['result' => $result]);
    }

    public function quotedProducts(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $currentStores = (isset($data['store']) && (int)$data['store'] > 0) ? [(int)$data['store']] : array_keys($stores->keyBy('id')->toArray());
            $columns = [
                'Factura',
                'Cliente',
                'Codigo de producto',
                'Producto',
                'Costo',
                'Precio',
                'Cantidad cotizada',
                'Cantidad disponible',
                'Cantidad en Bodega',
                'Equivalencias en Bodega',
                'Equivalencias Globales',
                'Ventas Equivalencias',
                'Vendedor',
                'Perfil'
            ];

            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'quoted_products_' . $currentDate[0] . $currentTime . '.csv';

            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            fclose($fp);

            $rows = count($currentStores);

            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'quoted_products';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));

            $reportProcess->save();

            $reportProcessId = $reportProcess->id;

            if ($rows == 1) {
                dispatch((new ReportQuotedProformProducts($nameFile, $reportProcessId, $currentStores, $from_date, $to_date, 'quoted'))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportQuotedProformProducts($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date, 'quoted'))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'quoted_products')->orderBy('id', 'DESC')->take(30)->get();

        return view('advancereports.quoted_products', ['reportProcess' => $reportProcess, 'stores' => $stores]);
    }

    public function proformProducts(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $currentStores = (isset($data['store']) && (int)$data['store'] > 0) ? [(int)$data['store']] : array_keys($stores->keyBy('id')->toArray());
            $columns = [
                'Factura',
                'Cliente',
                'Codigo de producto',
                'Producto',
                'Costo',
                'Precio',
                'Cantidad cotizada',
                'Cantidad disponible',
                'Cantidad en Bodega',
                'Equivalencias en Bodega',
                'Equivalencias Globales',
                'Ventas Equivalencias',
                'Vendedor',
                'Perfil'
            ];

            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'proform_products_' . $currentDate[0] . $currentTime . '.csv';

            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            fclose($fp);

            $rows = count($currentStores);

            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'proform_products';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));

            $reportProcess->save();

            $reportProcessId = $reportProcess->id;

            if ($rows == 1) {
                dispatch((new ReportQuotedProformProducts($nameFile, $reportProcessId, $currentStores, $from_date, $to_date, 'proform'))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportQuotedProformProducts($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date, 'proform'))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'proform_products')->orderBy('id', 'DESC')->take(30)->get();

        return view('advancereports.proform_products', ['reportProcess' => $reportProcess, 'stores' => $stores]);
    }

    public function noUpdatedProducts(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $query = DB::table('products_tracking')
                ->join('accounts', 'accounts.id', '=', 'products_tracking.original_account_id')
                ->select('products_tracking.*', 'accounts.name')
                ->where(DB::raw('original_quantity_before'), DB::raw('original_quantity_after'))
                ->whereIn('transaction_type', ['invoice', 'transfer'])
                ->where('products_tracking.created_at', '>=', $from_date)
                ->where('products_tracking.created_at', '<', $to_date);
            if ($store) {
                $query->where('original_account_id', $store);
            }
            $items = $query->get();
            foreach ($items as $item) {
                $result[] = [
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'qty' => $item->qty,
                    'original_quantity_before' => $item->original_quantity_before,
                    'original_quantity_after' => $item->original_quantity_after,
                    'name' => $item->name,
                    'created_at' => $item->created_at,
                    'transaction_type' => $item->transaction_type,
                    'invoice_id' => $item->invoice_id,
                    'transfer_id' => $item->transfer_id
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('no_updated_products', $result);
        }
        return view('advancereports.no_updated_products', ['result' => $result, 'stores' => $stores]);
    }

    public function vouchersDiscounts(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = [];
        if (isset($data['expoor_clients']) && $data['expoor_clients'] == 1) {
            $result = DB::table('clients')
                ->where('clients.amount_vouchers_kms', '>', 0)
                ->join('accounts', 'accounts.id', '=', 'clients.account_id')
                ->select(
                    'clients.id',
                    'clients.name',
                    'clients.vouchers_discount',
                    'clients.percentage_vouchers',
                    'clients.amount_vouchers_kms',
                    'clients.phone',
                    'accounts.name as account'
                )
                ->orderBy('clients.vouchers_discount', 'DESC')->get();

            return $this->export('vouchers_clients_discounts', $result);
        } else {
            if (count($data) > 0) {
                $from_date = $data['from_date'];
                $to_date = $data['to_date'];
                $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
                $store = isset($data['store']) ? $data['store'] : null;
                $query = DB::table('client_vouchers as cv')
                    ->join('clients', 'clients.id', '=', 'cv.client_id')
                    ->join('accounts', 'accounts.id', '=', 'cv.account_id')
                    ->select('cv.*', 'clients.name as clients_name', 'accounts.name as accounts_name')
                    ->whereDate('cv.created_at', '>=', $from_date)
                    ->whereDate('cv.created_at', '<', $to_date);
                if ($store) {
                    $query->where('cv.account_id', $store);
                }
                $result = $query->get();
            }
            if (isset($data['export']) && $data['export'] == 1) {
                return $this->export('vouchers_discounts', $result);
            }
            return view('advancereports.vouchers_discounts', ['result' => $result, 'stores' => $stores]);
        }
    }

    public function expensesCashCount(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $categories = ExpenseCategory::orderBy('name')->whereIn('state', [1, 2])->get();
        $subcategories = ExpenseSubcategory::join('expense_categories as ec', 'expense_category_id', '=', 'ec.id')->whereIn('ec.state', [1, 2])->select('expense_subcategories.*')->orderBy('code')->get();
        $financeAccounts = FinanceAccount::get();
        $result = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $query = DB::table('expenses as ex')
                ->leftJoin('expense_categories as ec', 'ec.id', '=', 'ex.expense_category_id')
                ->leftJoin('expense_subcategories as es', 'es.id', '=', 'ex.expense_subcategory_id')
                ->leftJoin('finance_accounts as fa', 'fa.id', '=', 'ex.finance_account_id')
                ->leftJoin('cash_count as cc', 'cc.id', '=', 'ex.cash_count_id')
                ->leftJoin('employees as em', 'em.id', '=', 'ex.real_employee_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'ex.account_id')
                ->leftJoin('users', 'users.id', '=', 'ex.user_id')
                ->select(
                    'ex.*',
                    'ec.name as categorie_name',
                    'es.name as subcategorie_name',
                    'es.code as code',
                    'fa.name as fa_name',
                    'accounts.name as account_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('cc.cash_count_date', '>=', $from_date)
                ->whereDate('cc.cash_count_date', '<', $to_date)
                /* ->where('is_approved', true) */
                ->orderBy('ex.id', 'desc');

            if ($store) {
                $query->where('ex.account_id', $store);
            }
            if ($data['expense_category_id']) {
                $query->where('ex.expense_category_id', $data['expense_category_id']);
            }
            if ($data['expense_subcategory_id']) {
                $query->where('ex.expense_subcategory_id', $data['expense_subcategory_id']);
            }
            if ($data['finance_account_id']) {
                $query->where('ex.finance_account_id', $data['finance_account_id']);
            }
            $result = $query->get();
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('cash_count_expenses', $result);
        }
        return view('advancereports.cash_count_expenses', [
            'result' => $result,
            'stores' => $stores,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'financeAccounts' => $financeAccounts
        ]);
    }

    public function expensesAllCashCount(Request $request)
    {
        $data = $request->all();
        $stores = Account::get()->keyBy('id');
        $stores_a = [];

        $categories = ExpenseCategory::orderBy('name')->whereIn('state', [1, 2])->get()->keyBy('id');
        $subcategories = ExpenseSubcategory::join('expense_categories as ec', 'expense_category_id', '=', 'ec.id')->whereIn('ec.state', [1, 2])->select('expense_subcategories.*')->orderBy('code')->get()->keyBy('expense_subcategories.id');
        $subcategories_all = ExpenseSubcategory::get()->keyBy('id');
        $results = [];
        if (count($data) > 0) {
            $stores_a = $data['store'] ? $stores_a[] = ['id' => $data['store']] : $stores;
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));

            foreach ($stores_a as $store) {
                $expenses = Expense::join('cash_count as cc', 'cc.id', '=', 'expenses.cash_count_id')
                    ->where('expenses.account_id', $store->id ?? $data['store'])
                    ->whereDate('cc.cash_count_date', '>=', $from_date)
                    ->whereDate('cc.cash_count_date', '<', $to_date)
                    /* ->where('expenses.is_approved', true) */
                    ->orderBy('expenses.expense_category_id', 'desc')
                    ->select('expenses.*')
                    ->get();

                $groupedExpenses = $expenses->groupBy('expense_category_id');

                foreach ($groupedExpenses as $categoryId => $categoryExpenses) {
                    $subcategoriesTotal = $categoryExpenses->groupBy('expense_subcategory_id')
                        ->map(function ($subcategoryExpenses) {
                            return $subcategoryExpenses->sum('amount');
                        });

                    $results[$store->id ?? $data['store']][$categoryId] = $subcategoriesTotal;
                }
            }
        }

        $results_all = [];
        if (isset($data['export']) && $data['export'] == 1) {
            foreach ($results as $store => $result) {
                foreach ($result as $categoryId => $item) {
                    foreach ($item as $subcategoryId => $monto) {
                        $storeName = $stores[$store]->name ?? 'No encontrado';
                        $categoryName = $categories[$categoryId]->name ?? 'No encontrado';
                        $subcategoryName = $subcategories_all[$subcategoryId]->name ?? 'No encontrado';

                        $results_all[$storeName][$categoryName][$subcategoryName] = $monto;
                    }
                }
            }

            return $this->export('cash_count_expenses_all', $results_all);
        }
        return view('advancereports.cash_count_expenses_all', [
            'results' => $results,
            'stores' => $stores,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'subcategories_all' => $subcategories_all
        ]);
    }

    public function transfersCashCount(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $financeAccounts = FinanceAccount::get();
        $result = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $query = DB::table('money_transfers as mt')
                ->leftJoin('finance_accounts as fa', 'fa.id', '=', 'mt.from_finance_id')
                ->leftJoin('finance_accounts as fat', 'fat.id', '=', 'mt.to_finance_id')
                ->join('users', 'users.id', '=', 'mt.user_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'mt.from_account_id')
                ->leftJoin('employees as em', 'em.id', '=', 'mt.real_employee_id')
                ->select(
                    'mt.*',
                    'fa.name as fa_name',
                    'fat.name as fat_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'accounts.name as account_name',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('mt.created_at', '>=', $from_date)
                ->whereDate('mt.created_at', '<', $to_date)
                ->orderBy('mt.id', 'desc');

            if ($data['from_finance_id']) {
                $query->where('mt.from_finance_id', $data['from_finance_id']);
            }
            if ($data['to_finance_id']) {
                $query->where('mt.to_finance_id', $data['to_finance_id']);
            }

            $result = $query->get();
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('cash_count_transfers', $result);
        }
        return view('advancereports.cash_count_transfers', [
            'result' => $result,
            'stores' => $stores,
            'financeAccounts' => $financeAccounts
        ]);
    }

    public function bankTransfersCashCount(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $financeAccounts = FinanceAccount::where('name', 'not like', "%efectivo%")->get();
        $paymentTypes = PaymentType::all();
        $fid = '';
        $pid = '';
        $result = [];
        if (count($data) > 0) {
            if ($data['finance_id']) {
                $fid =  FinanceAccount::find($data['finance_id']);
            }
            if ($data['pay_id']) {
                $pid = PaymentType::find($data['pay_id']);
            }
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;
            $query = DB::table('money_transfers as mt')
                ->leftJoin('finance_accounts as fa', 'fa.id', '=', 'mt.from_finance_id')
                ->leftJoin('finance_accounts as fat', 'fat.id', '=', 'mt.to_finance_id')
                ->join('users', 'users.id', '=', 'mt.user_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'mt.from_account_id')
                ->leftJoin('employees as em', 'em.id', '=', 'mt.real_employee_id')
                ->select(
                    'fa.name as fa_name',
                    'fat.name as fat_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'mt.document_number as number',
                    'mt.document_date as created_at',
                    'mt.completed as completed',
                    'mt.description as description',
                    'mt.cash_count_id as cash_count_id',
                    'mt.cash_count_out_id as cash_count_out_id',
                    'mt.amount as amount',
                    'mt.id as id',
                    'accounts.name as account_name',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('mt.created_at', '>=', $from_date)
                ->whereDate('mt.created_at', '<', $to_date)
                ->where('fat.name', 'not like', "%efectivo%")
                ->orderBy('mt.id', 'desc');
            if ($data['finance_id']) {
                $query->where('mt.to_finance_id', $data['finance_id']);
            }
            if ($pid) {
                $pname = str_replace('_', ' ', $pid->name);
                $query->where('fat.name', 'LIKE', "%$pname%");
                $query->orWhere('fat.name', 'LIKE', "%$pid->name%");
            } //ok
            $result = $query->get();

            $query2 = DB::table('payments as py')
                ->leftJoin('payment_types as pt', 'pt.id', '=', 'py.payment_type_id')
                ->join('users', 'users.id', '=', 'py.user_id')
                ->leftJoin('invoices as inv', 'inv.id', '=', 'py.invoice_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'py.account_id')
                ->leftJoin('employees as em', 'em.id', '=', 'py.real_employee_id')

                ->select(
                    'pt.name as fat_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'inv.invoice_number as number',
                    'py.payment_date as created_at',
                    'py.cash_count_id as cash_count_id',
                    'py.amount as amount',
                    'py.id as id',
                    'py.payment_status_id as completed',
                    'accounts.name as account_name',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('py.created_at', '>=', $from_date)
                ->whereDate('py.created_at', '<', $to_date)
                ->orderBy('py.id', 'desc');
            if ($data['pay_id']) {
                $query2->where('py.payment_type_id', $data['pay_id']);
            }
            if ($fid) {
                $fname = str_replace(' ', '_', $fid->name);
                $query2->where('pt.name', 'LIKE', "%$fid->name%");
                $query2->orWhere('pt.name', 'LIKE', "%$fname%");
            }

            $result2 = $query2->get();

            $query3 = DB::table('money_incomes as mi')
                ->leftJoin('payment_types as pt', 'pt.id', '=', 'mi.payment_type_id')
                ->join('users', 'users.id', '=', 'mi.user_id')
                ->leftJoin('income_categories as ic', 'ic.id', '=', 'mi.income_category_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'mi.account_id')
                ->leftJoin('employees as em', 'em.id', '=', 'mi.real_employee_id')

                ->select(
                    'pt.name as fat_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'mi.document_number as number',
                    'mi.document_date as created_at',
                    'mi.description as description',
                    'mi.cash_count_id as cash_count_id',
                    'mi.amount as amount',
                    'mi.id as id',
                    'mi.id as completed',
                    'accounts.name as account_name',
                    'ic.name as fa_name',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('mi.document_date', '>=', $from_date)
                ->whereDate('mi.document_date', '<', $to_date)
                ->orderBy('mi.id', 'desc');
            if ($data['pay_id']) {
                $query3->where('mi.payment_type_id', $data['pay_id']);
            }
            if ($fid) {
                $fname = str_replace(' ', '_', $fid->name);
                $query3->where('pt.name', 'LIKE', "%$fid->name%");
                $query3->orWhere('pt.name', 'LIKE', "%$fname%");
            }

            $result3 = $query3->get();

            $query4 = DB::table('store_credits as sc')
                ->leftJoin('payment_types as pt', 'pt.id', '=', 'sc.payment_type_id')
                ->join('users', 'users.id', '=', 'sc.user_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'sc.account_id')
                ->leftJoin('employees as em', 'em.id', '=', 'sc.real_employee_id')

                ->select(
                    'pt.name as fat_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'sc.credit_number as number',
                    'sc.credit_date as created_at',
                    'sc.private_notes as description',
                    'sc.cash_count_id as cash_count_id',
                    'sc.amount as amount',
                    'sc.id as id',
                    'sc.is_approved as completed',
                    'accounts.name as account_name',
                    'sc.id as store',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('sc.credit_date', '>=', $from_date)
                ->whereDate('sc.credit_date', '<', $to_date)
                ->orderBy('sc.id', 'desc');
            if ($data['pay_id']) {
                $query4->where('sc.payment_type_id', $data['pay_id']);
            }
            if ($fid) {
                $fname = str_replace(' ', '_', $fid->name);
                $query4->where('pt.name', 'LIKE', "%$fid->name%");
                $query4->orWhere('pt.name', 'LIKE', "%$fname%");
            }

            $result4 = $query4->get();

            $result = array_merge($result, $result2, $result3, $result4);
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('cash_count_bank_transfers', $result);
        }
        return view('advancereports.cash_count_bank_transfers', [
            'result' => $result,
            'stores' => $stores,
            'financeAccounts' => $financeAccounts,
            'paymentTypes' => $paymentTypes
        ]);
    }

    public function salesCashCount(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $paymentTypes = PaymentType::all();
        $result = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;

            $query = DB::table('payments as py')
                ->leftJoin('payment_types as pt', 'pt.id', '=', 'py.payment_type_id')
                ->join('users', 'users.id', '=', 'py.user_id')
                ->leftJoin('invoices as inv', 'inv.id', '=', 'py.invoice_id')
                ->join('accounts', 'accounts.id', '=', 'py.account_id')

                ->select(
                    'pt.name as fat_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'inv.invoice_number as number',
                    'py.payment_date as payment_date',
                    'py.cash_count_id as cash_count_id',
                    'py.id as id',
                    'py.payment_status_id as payment_status_id',
                    'inv.discount_percent as discount_percent',
                    'inv.discount_points as discount_points',
                    'inv.discount_vouchers as discount_vouchers',
                    'inv.discount as discount',
                    'inv.amount as amount',
                    'inv.total as total',
                    'accounts.name as account_name'
                )
                ->whereDate('py.created_at', '>=', $from_date)
                ->whereDate('py.created_at', '<', $to_date)
                ->orderBy('py.id', 'desc');
            if ($data['pay_id']) {
                $query->where('py.payment_type_id', $data['pay_id']);
            }
            if ($store) {
                $query->where('py.account_id', $store);
            }

            $result = $query->get();
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('cash_count_sales', $result);
        }
        return view('advancereports.cash_count_sales', [
            'result' => $result,
            'stores' => $stores,
            'paymentTypes' => $paymentTypes
        ]);
    }

    public function incomesCashCount(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $paymentTypes = PaymentType::all();
        $result = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = isset($data['store']) ? $data['store'] : null;

            $query = DB::table('money_incomes as mi')
                ->leftJoin('payment_types as pt', 'pt.id', '=', 'mi.payment_type_id')
                ->join('users', 'users.id', '=', 'mi.user_id')
                ->leftJoin('income_categories as ic', 'ic.id', '=', 'mi.income_category_id')
                ->join('accounts', 'accounts.id', '=', 'mi.account_id')
                ->leftJoin('cash_count as cc', 'cc.id', '=', 'mi.cash_count_id')
                ->leftJoin('employees as em', 'em.id', '=', 'mi.real_employee_id')

                ->select(
                    'pt.name as py_name',
                    'users.first_name as user_name',
                    'users.last_name as user_name2',
                    'mi.document_number as number',
                    'mi.created_at as created_at',
                    'mi.cash_count_id as cash_count_id',
                    'mi.id as id',
                    'accounts.name as account_name',
                    'ic.name as ca_name',
                    'mi.description',
                    'mi.amount',
                    'cc.cash_count_date as asig_date',
                    'em.first_name as employee_name',
                    'em.last_name as employee_name2'
                )
                ->whereDate('cc.cash_count_date', '>=', $from_date)
                ->whereDate('cc.cash_count_date', '<', $to_date)
                ->orderBy('mi.id', 'desc');
            if ($data['pay_id']) {
                $query->where('mi.payment_type_id', $data['pay_id']);
            }
            if ($store) {
                $query->where('mi.account_id', $store);
            }

            $result = $query->get();
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('cash_count_incomes', $result);
        }
        return view('advancereports.cash_count_incomes', [
            'result' => $result,
            'stores' => $stores,
            'paymentTypes' => $paymentTypes
        ]);
    }

    public function routes(Request $request)
    {

        $id_af = IncomeCategory::where('name', 'like', '%(MAYOREO) ABONO A FACTURAS%')->value('id');
        /* $id_oi = IncomeCategory::where('name', 'like', '%(MAYOREO) Otros Ingresos%')->value('id');
        $id_cp = IncomeCategory::where('name', 'like', '%(MAYOREO) COMICIONES POS%')->value('id'); */

        $data = $request->all();
        $stores = Account::where('name', 'like', "%ruta%")->orderBy('id', 'asc')->get();
        $paymentTypes = PaymentType::all();
        $result = [];
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $store = isset($data['store']) ? $data['store'] : null;
            $type = $data['reporte'];

            if ($type == "contado") {
                $query = DB::table('invoices as inv')
                    ->leftJoin('payments as py', 'inv.id', '=', 'py.invoice_id')
                    ->leftJoin('payment_types as pt', 'pt.id', '=', 'py.payment_type_id')
                    ->join('accounts', 'accounts.id', '=', 'inv.account_id')
                    ->join('users', 'users.id', '=', 'inv.user_id')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'inv.employee_id')
                    ->leftJoin('routes as ro', 'ro.account_id', '=', 'emp.account_id')

                    ->select(
                        'pt.name as fat_name',
                        'users.first_name as user_name',
                        'users.last_name as user_name2',
                        'inv.invoice_number as number',
                        'inv.date_changed_credit as payment_date',
                        'inv.cash_count_id as cash_count_id',
                        'inv.id as id',
                        'py.payment_status_id as payment_status_id',
                        'inv.discount_percent as discount_percent',
                        'inv.discount_points as discount_points',
                        'inv.discount_vouchers as discount_vouchers',
                        'inv.discount as discount',
                        'inv.amount as amount',
                        'inv.balance as balance',
                        'accounts.name as account_name',
                        'ro.name as route_name',
                        'emp.first_name as emp_name',
                        'emp.last_name as emp_name2'
                    )
                    ->where('inv.cash_count_id', '!=', null)
                    ->whereDate('inv.date_changed_credit', '>=', $from_date)
                    ->whereDate('inv.date_changed_credit', '<', $to_date)
                    ->orderBy('inv.invoice_number', 'desc')
                    ->groupBy('inv.id');

                if ($data['pay_id']) {
                    $query->where('py.payment_type_id', $data['pay_id']);
                }
                if ($store) {
                    $query->where('inv.account_id', $store);
                }
            } elseif ($type == "credito") {
                $query = DB::table('invoices as inv')
                    ->join('accounts', 'accounts.id', '=', 'inv.account_id')
                    ->join('users', 'users.id', '=', 'inv.user_id')
                    ->leftJoin('cash_count as cc', 'cc.cash_count_date', '=', 'inv.date_changed_credit')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'inv.employee_id')
                    ->leftJoin('routes as ro', 'ro.account_id', '=', 'emp.account_id')

                    ->select(
                        'users.first_name as user_name',
                        'users.last_name as user_name2',
                        'inv.invoice_number as number',
                        'inv.date_changed_credit as payment_date',
                        'cc.id as cash_count_id',
                        'inv.id as id',
                        'inv.discount_percent as discount_percent',
                        'inv.discount_points as discount_points',
                        'inv.discount_vouchers as discount_vouchers',
                        'inv.discount as discount',
                        'inv.amount as amount',
                        'inv.balance as balance',
                        'accounts.name as account_name',
                        'ro.name as route_name',
                        'emp.first_name as emp_name',
                        'emp.last_name as emp_name2'
                    )
                    ->where('inv.cash_count_id', null)
                    ->where('inv.is_credit', 1)
                    ->where('inv.in_transit', 0)
                    ->whereDate('inv.date_changed_credit', '>=', $from_date)
                    ->whereDate('inv.date_changed_credit', '<', $to_date)
                    ->groupBy('inv.id');
                if ($store) {
                    $query->where('inv.account_id', $store);
                }
            } elseif ($type == "abono") {
                $query = DB::table('money_incomes as mi')
                    ->leftJoin('payment_types as pt', 'pt.id', '=', 'mi.payment_type_id')
                    ->join('users', 'users.id', '=', 'mi.user_id')
                    ->leftJoin('income_categories as ic', 'ic.id', '=', 'mi.income_category_id')
                    ->join('accounts', 'accounts.id', '=', 'mi.account_id')
                    ->leftJoin('cash_count as cc', 'cc.id', '=', 'mi.cash_count_id')
                    ->leftJoin('employees as em', 'em.id', '=', 'mi.real_employee_id')
                    ->leftJoin('routes as ro', 'ro.account_id', '=', 'mi.account_id')

                    ->select(
                        'pt.name as fat_name',
                        'users.first_name as user_name',
                        'users.last_name as user_name2',
                        'mi.document_number as number',
                        'mi.created_at as payment_date',
                        'mi.cash_count_id as cash_count_id',
                        'mi.id as id',
                        'accounts.name as account_name',
                        'mi.amount as amount',
                        'ic.name as ca_name',
                        'mi.description',
                        'ro.name as route_name'
                    )

                    ->where('ic.id', $id_af)
                    ->whereDate('cc.cash_count_date', '>=', $from_date)
                    ->whereDate('cc.cash_count_date', '<', $to_date)
                    ->orderBy('mi.id', 'desc');
                if ($data['pay_id']) {
                    $query->where('mi.payment_type_id', $data['pay_id']);
                }
                if ($store) {
                    $query->where('mi.account_id', $store);
                }
            } elseif ($type == "otros") {
                $query = DB::table('money_incomes as mi')
                    ->leftJoin('payment_types as pt', 'pt.id', '=', 'mi.payment_type_id')
                    ->join('users', 'users.id', '=', 'mi.user_id')
                    ->leftJoin('income_categories as ic', 'ic.id', '=', 'mi.income_category_id')
                    ->join('accounts', 'accounts.id', '=', 'mi.account_id')
                    ->leftJoin('cash_count as cc', 'cc.id', '=', 'mi.cash_count_id')
                    ->leftJoin('employees as em', 'em.id', '=', 'mi.real_employee_id')
                    ->leftJjoin('routes as ro', 'ro.account_id', '=', 'mi.account_id')

                    ->select(
                        'pt.name as fat_name',
                        'users.first_name as user_name',
                        'users.last_name as user_name2',
                        'mi.document_number as number',
                        'mi.created_at as payment_date',
                        'mi.cash_count_id as cash_count_id',
                        'mi.id as id',
                        'accounts.name as account_name',
                        'mi.amount as amount',
                        'ic.name as ca_name',
                        'mi.description'
                    )

                    ->where('ic.id', '!=', $id_af)
                    ->whereDate('cc.cash_count_date', '>=', $from_date)
                    ->whereDate('cc.cash_count_date', '<', $to_date)
                    ->orderBy('mi.id', 'desc');
                if ($data['pay_id']) {
                    $query->where('mi.payment_type_id', $data['pay_id']);
                }
                if ($store) {
                    $query->where('mi.account_id', $store);
                }
            } elseif ($type == "meta_vendido") {
                if ($store) {
                    $routes = Route::where('account_id', $store)->get();
                } else {
                    $routes = Route::whereNull('deleted')->get();
                }
                $result = [];
                foreach ($routes as $route) {
                    $result[$route->name] = Auth::user()->clients_atended($route->id ?? '', $from_date, $to_date);
                }
            } else {
                $query = DB::table('invoices as inv')
                    ->join('accounts', 'accounts.id', '=', 'inv.account_id')
                    ->join('users', 'users.id', '=', 'inv.user_id')
                    ->leftJoin('cash_count as cc', 'cc.cash_count_date', '=', 'inv.date_changed_credit')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'inv.employee_id')
                    ->leftJoin('routes as ro', 'ro.account_id', '=', 'emp.account_id')

                    ->select(
                        'users.first_name as user_name',
                        'users.last_name as user_name2',
                        'inv.invoice_number as number',
                        'inv.date_changed_credit as payment_date',
                        'cc.id as cash_count_id',
                        'inv.id as id',
                        'inv.discount_percent as discount_percent',
                        'inv.discount_points as discount_points',
                        'inv.discount_vouchers as discount_vouchers',
                        'inv.discount as discount',
                        'inv.amount as amount',
                        'inv.balance as balance',
                        'accounts.name as account_name',
                        'ro.name as route_name',
                        'emp.first_name as emp_name',
                        'emp.last_name as emp_name2'
                    );
                if ($data['no_factura']) {
                    $query->where('inv.invoice_number', $data['no_factura']);
                }
            }

            if ($type != "meta_vendido") {
                $result = $query->get();
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            if ($type != "meta_vendido") {
                return $this->export('cash_count_routes', $result);
            } else {
                return $this->export('cash_count_routes_state', $result);
            }
        }
        return view('advancereports.cash_count_routes', [
            'result' => $result,
            'stores' => $stores,
            'paymentTypes' => $paymentTypes,
            'data' => $data
        ]);
    }

    public function salesBySac(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));

            $invoices = DB::table('invoices')->join('clients', 'invoices.client_id', '=', 'clients.id')
                ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
                ->select(
                    'invoices.total_cost AS total_cost',
                    'invoices.amount AS amount',
                    DB::raw('invoices.invoice_number as invoice'),
                    'clients.name',
                    'clients.address1',
                    'clients.work_phone',
                    'clients.phone',
                    'clients.id',
                    'clients.type',
                    'invoices.seller_id',
                    'invoices.auxiliar_id',
                    'invoices.invoice_date as invoice_date',
                    'accounts.name as account_name'
                )
                ->where('accounts.exclude', 0)
                ->whereNotNull('invoices.auxiliar_id')
                ->where('invoice_type_id', 1)
                ->whereDate('invoices.invoice_date', '>=', $from_date)
                ->whereDate('invoices.invoice_date', '<', $to_date)
                ->groupBy('invoices.client_id')->get();

            $employees = Employee::all()->keyBy('id');
            $result = [];

            foreach ($invoices as $invoice) {
                $result[] = [
                    'employee_sac' => isset($employees[$invoice->auxiliar_id]) ? ($employees[$invoice->auxiliar_id]->first_name . " " . $employees[$invoice->auxiliar_id]->last_name) : '',
                    'name' => $invoice->name,
                    'address' => $invoice->address1,
                    'account_name' => $invoice->account_name,
                    'phone' => $invoice->work_phone ? $invoice->work_phone : $invoice->phone,
                    'employee' => isset($employees[$invoice->seller_id]) ? ($employees[$invoice->seller_id]->first_name . " " . $employees[$invoice->seller_id]->last_name) : '',
                    'total' => $invoice->amount,
                    'invoice' => $invoice->invoice,
                    'invoice_date' => $invoice->invoice_date,
                    'total_cost' => $invoice->total_cost
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('sales_by_sac', $result);
        }
        return view('advancereports.sales_by_sac', ['result' => $result]);
    }

    public function routesVisits(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];

            $visits = '';
            $visits = DB::table('routes')
                ->whereNull('deleted')
                ->join('clients as c', 'c.account_id', '=', 'routes.account_id')
                ->join('users as us', 'us.id', '=', 'routes.user_id')
                ->leftJoin('visits as vs', 'vs.client_id', '=', 'c.id')
                ->whereDate('vs.created_at', '>=', $from_date)
                ->whereDate('vs.created_at', '<=', $to_date)
                ->select(
                    'routes.id',
                    'routes.name',
                    'us.username as user',
                    'vs.created_at',
                    DB::raw('(SELECT COUNT(DISTINCT id) FROM clients WHERE account_id = routes.account_id) as num_clients'),
                    DB::raw('COUNT(DISTINCT vs.client_id) as num_visits')
                )
                ->groupBy('routes.id', 'routes.name')
                ->orderBy('routes.id', 'asc')
                ->get();

            $result = [];
            $to_date2 = $to_date != $from_date ? ' al ' . $to_date : '';
            foreach ($visits as $visit) {
                $porcentaje = ($visit->num_visits / $visit->num_clients) * 100;

                $visits_outside_day = DB::table('visits')
                    ->join('clients as c', 'c.id', '=', 'visits.client_id')
                    ->join('routes as r', 'r.account_id', '=', 'c.account_id')
                    ->where('r.id', $visit->id)
                    ->whereDate('visits.created_at', '>=', $from_date)
                    ->whereDate('visits.created_at', '<=', $to_date)
                    ->select('visits.created_at', 'c.frequency_day', 'c.route_id')
                    ->groupBy('visits.id')
                    ->get();


                // Filtrar solo las visitas que ocurran en días diferentes al asignado en 'route_clients'
                $filtered_visits = [];
                foreach ($visits_outside_day as $vi) {
                    $created_day_number = date('N', strtotime($vi->created_at)); // Obtener el número del día de la semana para la visita
                    $frequency_day_number = $this->mapDayOfWeek($vi->frequency_day); // Obtener el número del día de la semana para el día asignado

                    if ($created_day_number != $frequency_day_number) {
                        $filtered_visits[] = $vi;
                    }
                }


                $result[] = [
                    'id' => $visit->id,
                    'name' => $visit->name,
                    'user' => $visit->user,
                    'clients' => $visit->num_clients,
                    'visits' => $visit->num_visits,
                    'fecha' => $from_date . $to_date2,
                    'percentage' => round($porcentaje, 2) . '%',
                    'other_visits' => count($filtered_visits)
                ];
            }
        }

        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('routes_visits', $result);
        }
        return view('advancereports.routes_visits', ['result' => $result]);
    }

    public function routesVisitsDay(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $date = $data['date'];

            $route = new Route();
            $result = $route->getVisitsRoutes($date);
        }

        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('routes_visits', $result);
        }
        return view('advancereports.routes_visits_day', ['result' => $result]);
    }

    public function mapDayOfWeek($dayName)
    {
        switch ($dayName) {
            case 'Lunes':
                return 1;
            case 'Martes':
                return 2;
            case 'Miércoles':
                return 3;
            case 'Jueves':
                return 4;
            case 'Viernes':
                return 5;
            case 'Sábado':
                return 6;
            case 'Domingo':
                return 7;
            default:
                return null;
        }
    }

    public function clientsPassed(Request $request)
    {
        $data = $request->all();
        $result = null;
        $routes = DB::table('routes')->whereNull('deleted')->get();

        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $route_id = $data['route_id'];


            $query = DB::table('clients')
                ->join('route_clients as rc', 'rc.client_id', '=', 'clients.id')
                ->join('invoices as in', 'in.client_id', '=', 'clients.id')
                ->join('accounts as ac', 'ac.id', '=', 'clients.account_id')
                ->whereNull('clients.deleted_at')
                ->whereNotNull('clients.route_name')
                ->where('in.invoice_status_id', 6)
                ->whereDate('clients.created_at', '>=', $from_date)
                ->whereDate('clients.created_at', '<=', $to_date)
                ->select(
                    'ac.name as account_name',
                    'clients.id',
                    'clients.company_name',
                    'clients.name',
                    'clients.phone',
                    'clients.created_at',
                    'clients.address1',
                    'clients.type',
                    'clients.route_name',
                    DB::raw('SUM(in.amount) as total_amount')
                )
                ->groupBy('clients.id')
                ->havingRaw('total_amount > 2500')
                ->orderBy('clients.id', 'asc');
            if ($route_id) {
                $query->where('rc.route_id', $route_id);
            }
            $clients = $query->get();



            foreach ($clients as $client) {

                $result[] = [
                    'id' => $client->id,
                    'account_name' => $client->account_name,
                    'name' => $client->name,
                    'company_name' => $client->company_name,
                    'phone' => $client->phone,
                    'created_at' => $client->created_at,
                    'address1' => $client->address1,
                    'type' => $client->type,
                    'route_name' => $client->route_name,
                    'total_amount' => $client->total_amount
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('clients_passed', $result);
        }
        return view('advancereports.clients_passed', ['result' => $result, 'routes' => $routes]);
    }

    public function clientsUnvisited(Request $request)
    {
        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];

            // Obtener la lista de IDs de clientes con visitas
            $visits = DB::table('route_clients as rc')
                ->join('clients as c', 'c.id', '=', 'rc.client_id')
                ->join('accounts as ac', 'ac.id', '=', 'c.account_id')
                ->join('visits', 'visits.client_id', '=', 'c.id')
                ->whereDate('visits.created_at', '>=', $from_date)
                ->whereDate('visits.created_at', '<=', $to_date)
                ->pluck('c.id');

            // Obtener clientes que no tienen visitas
            $clients = DB::table('route_clients as rc')
                ->join('clients as c', 'c.id', '=', 'rc.client_id')
                ->join('accounts as ac', 'ac.id', '=', 'c.account_id')
                ->whereNotIn('c.id', $visits)
                ->select(
                    'ac.name as account_name',
                    'c.id',
                    'c.company_name',
                    'c.name',
                    'c.phone',
                    'rc.frequency_day',
                    'c.address1',
                    'c.type',
                    'c.route_name'
                )
                ->orderBy('c.id', 'asc')
                ->get();

            foreach ($clients as $client) {

                $result[] = [
                    'id' => $client->id,
                    'account_name' => $client->account_name,
                    'name' => $client->name,
                    'company_name' => $client->company_name,
                    'phone' => $client->phone,
                    'frequency_day' => $client->frequency_day,
                    'address1' => $client->address1,
                    'type' => $client->type,
                    'route_name' => $client->route_name
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('clients_unvisited', $result);
        }
        return view('advancereports.clients_unvisited', ['result' => $result]);
    }

    public function promisesPay(Request $request)
    {
        $data = $request->all();
        $result = null;
        $routes = DB::table('routes')->whereNull('deleted')->get();

        if (count($data) > 0) {
            $route_id = $data['route_id'];

            $query = DB::table('clients_blocked_history as cbh')
                ->join('clients as c', 'c.id', '=', 'cbh.client_id')
                ->leftJoin('employees as em', 'em.id', '=', 'c.seller_id')
                ->leftJoin('users as u', 'u.id', '=', 'cbh.unlocked_by')
                ->leftJoin('routes as r', 'r.account_id', '=', 'em.account_id')
                ->select(
                    'cbh.id',
                    'r.name as route_name',
                    'em.first_name as seller_name1',
                    'em.last_name as seller_name2',
                    'cbh.client_id',
                    'c.phone',
                    'c.blocked_credit',
                    'c.name as client_name',
                    'c.company_name as company_name',
                    'cbh.is_blocked',
                    'cbh.blocked_at',
                    'cbh.blocked_by',
                    'cbh.balance',
                    'cbh.limit_credit',
                    'cbh.unlocked_at',
                    'cbh.unlocked_by',
                    'cbh.payment_promise',
                    'cbh.comments_promise',
                    'u.username'
                )
                ->orderBy('r.name', 'asc');

            if ($data['from_date']) {
                $query->where('cbh.payment_promise', '>=', $data['from_date']);
            }
            if ($data['to_date']) {
                $query->where('cbh.payment_promise', '<=', $data['to_date']);
            }

            if ($route_id) {
                $route = Route::find($route_id);
                $clients = Auth::user()->clients_account($route->account_id);
                $clientIds = $clients->pluck('id');

                $query->whereIn('c.id', $clientIds);
            }

            $result = $query->get();
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('promises_pay', $result);
        }
        return view('advancereports.promises_pay', ['results' => $result, 'routes' => $routes]);
    }


    public function per_diem(Request $request)
    {
        $data = $request->all();
        $result = null;
        $employees = DB::table('employees')->where('enabled', 1)->get();

        if (count($data) > 0) {

            $query = DB::table('travel_expenses as te')
                ->leftJoin('employees as em', 'em.id', '=', 'te.employee_id')
                ->leftJoin('users as u', 'u.id', '=', 'te.user_id')
                ->leftJoin('users as u_a', 'u_a.id', '=', 'te.id_aproved')
                ->leftJoin('users as u_p', 'u_p.id', '=', 'te.user_id_paid')
                ->select(
                    'te.id',
                    'em.first_name as name1',
                    'em.last_name as name2',
                    'u.username as user_create',
                    'u_a.username as user_aproved',
                    'u_p.username as user_paid',
                    'te.status',
                    'te.total_amount',
                    'te.generals_comments',
                    'te.total_amount_paid',
                    'te.date_paid',
                    'te.created_at',
                    'te.total_declared',
                    'te.total_cuadre'
                )
                ->orderBy('te.id', 'asc');

            if ($data['from_date']) {
                $query->where('te.created_at', '>=', $data['from_date']);
            }
            if ($data['to_date']) {
                $query->where('te.created_at', '<=', $data['to_date']);
            }
            if ($data['employee_id']) {
                $query->where('em.id', $data['employee_id']);
            }

            $dats = $query->get();

            foreach ($dats as $dat) {
                $result[] = [
                    'id' => $dat->id,
                    'name1' => $dat->name1,
                    'name2' => $dat->name2,
                    'status' => $dat->status,
                    'total_amount' => $dat->total_amount,
                    'total_amount_paid' => $dat->total_amount_paid,
                    'total_declared' => $dat->total_declared,
                    'total_cuadre' => $dat->total_cuadre,
                    'created_at' => $dat->created_at,
                    'date_paid' => $dat->date_paid,
                    'user_create' => $dat->user_create,
                    'user_aproved' => $dat->user_aproved,
                    'user_paid' => $dat->user_paid,
                    'generals_comments' => $dat->generals_comments,
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            $fileName = 'Reporte_Mayorista' . date('Y_m_d_H_i') . '.csv';
            $columns = [
                'ID',
                'First Name',
                'Last Name',
                'Status',
                'Total Amount',
                'Total Amount Paid',
                'Total Declared',
                'Total Cuadre',
                'Created At',
                'Date Paid',
                'User Create',
                'User Approved',
                'User Paid',
                'General Comments'
            ];

            $filePath = tempnam(sys_get_temp_dir(), 'csv');
            $file = fopen($filePath, 'w');

            fputcsv($file, $columns);

            foreach ($result as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['name1'],
                    $row['name2'],
                    $row['status'],
                    $row['total_amount'],
                    $row['total_amount_paid'],
                    $row['total_declared'],
                    $row['total_cuadre'],
                    $row['created_at'],
                    $row['date_paid'],
                    $row['user_create'],
                    $row['user_aproved'],
                    $row['user_paid'],
                    $row['generals_comments']
                ]);
            }

            fclose($file);

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
        return view('advancereports.per_diem', ['results' => $result, 'employees' => $employees]);
    }

    public function categoryClients(Request $request)
    {
        $data = $request->all();
        $results = null;
        $routes = DB::table('routes')->whereNull('deleted')->get();

        if (count($data) > 0) {
            $route_id = $data['route_id'];

            $query = DB::table('clients')
                ->join('routes as r', 'r.account_id', '=', 'clients.account_id')
                ->join('invoices as in', 'in.client_id', '=', 'clients.id')
                ->join('accounts as ac', 'ac.id', '=', 'clients.account_id')
                ->whereNull('clients.deleted_at')
                ->where('in.invoice_status_id', '!=', 3)
                ->whereDate('in.created_at', '>=', $data['from_date'])
                ->whereDate('in.created_at', '<=', $data['to_date'])
                ->select(
                    'ac.name as account_name',
                    'clients.id',
                    'clients.company_name',
                    'clients.name',
                    'clients.phone',
                    'clients.created_at',
                    'clients.address1',
                    'clients.type',
                    'r.name as route_name',
                    DB::raw('SUM(in.amount) as total_amount'),
                    DB::raw('CASE
                                WHEN SUM(in.amount) BETWEEN 0 AND 5000 THEN "C"
                                WHEN SUM(in.amount) BETWEEN 5001 AND 10000 THEN "B"
                                WHEN SUM(in.amount) BETWEEN 10001 AND 50000 THEN "BB"
                                WHEN SUM(in.amount) BETWEEN 50001 AND 100000 THEN "A"
                                WHEN SUM(in.amount) BETWEEN 100001 AND 150000 THEN "AA"
                                ELSE "AAA"
                        END as category')
                )
                ->groupBy('clients.id')
                ->orderBy('clients.id', 'asc');
            if ($route_id) {
                $query->where('r.id', $route_id);
            }
            $clients = $query->get();

            foreach ($clients as $client) {

                $results[] = [
                    'id' => $client->id,
                    'name' => $client->name,
                    'company_name' => $client->company_name,
                    'phone' => $client->phone,
                    'created_at' => $client->created_at,
                    'address1' => $client->address1,
                    'type' => $client->type,
                    'route_name' => $client->route_name,
                    'total_amount' => $client->total_amount,
                    'category' => $client->category
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('category_clients', $clients);
        }
        return view('advancereports.category_clients', ['results' => $results, 'routes' => $routes]);
    }

    public function documentationClients(Request $request)
    {
        $data = $request->all();
        $results = null;
        $routes = Route::whereNull('deleted')->get();
        $a_routes = $routes->pluck('account_id');
        if (count($data) > 0) {
            $route_id = $data['route_id'];

            $query = DB::table('clients')
                ->join('routes as r', 'r.account_id', '=', 'clients.account_id')
                ->join('accounts as ac', 'ac.id', '=', 'clients.account_id')
                ->whereNull('clients.deleted_at')
                ->select(
                    'ac.name as account_name',
                    'clients.id',
                    'clients.company_name',
                    'clients.name',
                    'clients.phone',
                    'clients.created_at',
                    'clients.address1',
                    'clients.type',
                    'r.name as route_name',
                    'clients.extra_attributes'
                )
                ->groupBy('clients.id')
                ->orderBy('clients.id', 'asc');
            if ($route_id) {
                $query->where('r.id', $route_id);
            } else {
                $query->whereIn('ac.id', $a_routes);
            }
            $clients = $query->get();

            foreach ($clients as $client) {
                $results[] = [
                    'id' => $client->id,
                    'name' => $client->name,
                    'company_name' => $client->company_name,
                    'phone' => $client->phone,
                    'created_at' => $client->created_at,
                    'address1' => $client->address1,
                    'type' => $client->type,
                    'route_name' => $client->route_name,
                    'extra_attributes' => json_decode($client->extra_attributes, true)
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('documentation_clients', $results);
        }
        return view('advancereports.documentation_clients', ['results' => $results, 'routes' => $routes]);
    }

    private function export($reportType, $displayData)
    {
        try {
            //no puede ser mayor a 31 caracteres
            $fileNam = substr($reportType, 0, 20);
            $fileName = $fileNam . date('Y_m_d');

            $user = Auth::user();
            $date = date('Y-m-d');
            $data = [
                'result' => $displayData,
                'isExport' => true
            ];
            error_reporting(0);
            return Excel::create($fileName, function ($excel) use ($user, $data, $reportType, $fileName) {
                $excel->setTitle($fileName)
                    ->setCreator($user->getDisplayName())
                    ->setLastModifiedBy($user->getDisplayName())
                    ->setDescription('')
                    ->setSubject('')
                    ->setKeywords('')
                    ->setCategory('')
                    ->setManager('')
                    ->setCompany($user->account->getDisplayName());

                $excel->sheet($fileName, function ($sheet) use ($reportType, $data) {
                    $sheet->loadView("advancereports.$reportType" . "_export", $data);
                });
            })->download('xls');
        } catch (\Exception $e) {
            dd('Error exporting report: ' . $e->getMessage());
        }
    }

    public function productsByVendor(Request $request)
    {
        $data = $request->all();
        $stores = Account::all();
        $result = null;
        if (count($data) > 0) {
            /* $from_date = $data['from_date'];
                    $to_date = $data['to_date'];
                    $to_date = date('Y-m-d', strtotime($to_date. ' + 1 days')); */
            $store = isset($data['store']) ? $data['store'] : null;
            $products = [];
            if ($store) {
                $products = DB::table('products')
                    ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
                    ->join('accounts', 'accounts.id', '=', 'products.account_id')
                    ->select('products.product_key', 'products.qty', 'products.cost', 'vendors.name', 'products.vendor_id')
                    ->where('accounts.exclude', 0)->whereNull('products.deleted_at') //->where('products.qty','>',0)
                    ->where('products.account_id', $store)
                    ->get();
            } else {
                $products = DB::table('products')
                    ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
                    ->join('accounts', 'accounts.id', '=', 'products.account_id')
                    ->select('products.product_key', 'products.qty', 'products.cost', 'vendors.name', 'products.vendor_id')
                    ->where('accounts.exclude', 0)->whereNull('products.deleted_at') //->where('products.qty','>',0)
                    ->get();
            }

            $result = [];
            foreach ($products as $item) {
                if (isset($result[$item->vendor_id]) == false) {
                    $result[$item->vendor_id] = [
                        'qty' => 0,
                        'total' => 0,
                        'vendor' => trim($item->name),
                        // 'cost' => 0,
                    ];
                }
                /* if (intval($item->qty) !== 0) {
                                    $result[$item->vendor_id]['cost'] += floatval($item->cost);
                            } else {
                                    $result[$item->vendor_id]['cost'] += 0;
                            } */

                $result[$item->vendor_id]['qty'] += intval($item->qty);
                $result[$item->vendor_id]['total'] += floatval(intval($item->qty) * floatval($item->cost));
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('products_by_vendor', $result);
        }
        return view('advancereports.products_by_vendor', ['result' => $result, 'stores' => $stores]);
    }
    public function finishReport($id)
    {
        $reportProcess = ReportProcess::find($id);

        $to_date = Carbon::now();
        $to_date = $to_date->toDateTimeString();

        $reportProcess->updated_at = $to_date;
        $reportProcess->status = 1;
        $reportProcess->save();

        Session::flash('message', "Proceso marcado como finalizado");
        return redirect()->back();
    }

    public function tracesRequest(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get();
        $areas = CompanyAreas::whereNull('deleted_at')->get();
        if (count($data) > 0) {
            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');

            $from_date = $data['from_date'] ? $data['from_date'] : $current_date;
            $to_date = $data['to_date'] ? $data['to_date'] : $current_date;

            $initDate = strtotime(date("d-m-Y H:i:s", strtotime($from_date)));
            $finishDate = strtotime(date("d-m-Y H:i:s", strtotime($to_date)));

            if ($initDate > $finishDate) {
                $newDate = $from_date;
                $from_date = $to_date;
                $to_date = $newDate;
            }
            $currentStores = (isset($data['store']) && $data['store'] !== 'all') ? [(int)$data['store']] : $stores->pluck('id')->toArray();
            $currentAreas = (isset($data['area']) && $data['area'] !== 'all') ? [(int)$data['area']] : $areas->pluck('id')->toArray();
            $tracesRequests = TracesRequest::whereIn('account_id', $currentStores)
                ->whereIn('company_areas_id', $currentAreas)
                ->where(function ($query) use ($from_date, $to_date) {
                    $query->where(function ($q) use ($from_date, $to_date) {
                        $q->whereDate('created_at', '>=', $from_date)
                            ->whereDate('created_at', '<', $to_date);
                    })
                        ->orWhere(function ($q) use ($from_date, $to_date) {
                            $q->whereDate('updated_at', '>=', $from_date)
                                ->whereDate('updated_at', '<', $to_date);
                        });
                })
                ->with('items')
                ->orderBy('id', 'desc')
                ->get();
            $stores_id = $stores->pluck('name', 'id')->toArray();
            $areas_id = $areas->pluck('name', 'id')->toArray();
            $columns = [
                'Identificador',
                'Tienda',
                'Area',
                'Creacion de Solicitud',
                'Supervisado',
                'Completado',
                'Actualizacion de Solicitud',
                'Descripcion de Item',
                'Prioridad',
                'Empleado Asignado',
                'Chequeo',
                'Actualizacion de Item',
            ];
            $name = 'reporte_solicitudes_' . str_replace('-', '_', $current_date) . '.csv';
            $fp = fopen($name, 'w');
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($tracesRequests as $tracesRequest) {
                foreach ($tracesRequest->items as $item) {
                    $result = [
                        'id' => $tracesRequest->id,
                        'account' => $stores_id[$tracesRequest->account_id],
                        'area' => $areas_id[$tracesRequest->company_areas_id],
                        'traces_request_created_at' => isset($tracesRequest->created_at) ? $tracesRequest->created_at->toDateString() : '',
                        'is_verify' => ($tracesRequest->is_verify == 1) ? 'Supervisado' : 'Pendiente',
                        'is_complete' => ($tracesRequest->is_complete == 1) ? 'Completado' : 'Pendiente',
                        'traces_request_updated_at' => isset($tracesRequest->updated_at) ? $tracesRequest->updated_at->toDateString() : '',
                        'description' => $item->description,
                        'priority' => ($item->priority == 0 || trim($item->priority) == '') ? 'Por Asignar' : (($item->priority == 1) ? 'Baja' : (($item->priority == 2) ? 'Media' : 'Alta')),
                        'assigned_employee_id' => isset($item->employee) ? $item->employee->name : '',
                        'is_check' => ($item->is_check == 0) ? 'Pendiente' : (($item->is_check == 1) ? 'En Proceso' : 'Finalizado'),
                        'item_updated_at' => isset($item->updated_at) ? $item->updated_at->toDateString() : '',
                    ];
                    fputcsv($fp, $result, ';');
                }
            }
            fclose($fp);
            return redirect('/' . $name);
        }
        return view('advancereports.traces_request', ['result' => null, 'stores' => $stores, 'areas' => $areas]);
    }

    public function cashCountNetSales(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get();
        if (count($data) > 0) {
            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');

            $from_date = $data['from_date'] ? $data['from_date'] : $current_date;
            $to_date = $data['to_date'] ? $data['to_date'] : $current_date;

            $initDate = strtotime(date("d-m-Y H:i:s", strtotime($from_date)));
            $finishDate = strtotime(date("d-m-Y H:i:s", strtotime($to_date)));

            if ($initDate > $finishDate) {
                $newDate = $from_date;
                $from_date = $to_date;
                $to_date = $newDate;
            }
            $columns = [];
            $date1 = new \DateTime($from_date);
            $date2 = new \DateTime($to_date);
            $days  = $date2->diff($date1)->format('%a');
            for ($i = 0; $i < $days; $i++) {
                $newDate = strtotime($from_date . " + $i days");
                $_date = date('Y-m-d', $newDate);
                $columns[$_date] = 0;
            }

            $currentStores = (isset($data['store']) && $data['store'] !== 'all') ? [(int)$data['store']] : $stores->pluck('id')->toArray();

            $cashcounts = CashCount::select(['id', 'account_id', 'cash_count_date', 'total_sales', 'refunds_cash'])
                ->whereIn('account_id', $currentStores)
                ->where(function ($query) use ($from_date, $to_date) {
                    $query->whereDate('cash_count_date', '>=', $from_date)
                        ->whereDate('cash_count_date', '<=', $to_date);
                })->get();

            $paymentTypeId = PaymentType::where('name', 'Credito Por Devolución')->first()->id;
            $creditRefunds = DB::table('payments')
                ->where('payment_type_id', $paymentTypeId)
                ->whereIn('cash_count_id', $cashcounts->pluck('id')->toArray())
                ->select(
                    'cash_count_id',
                    DB::raw('SUM(amount) as amount')
                )->groupBy('cash_count_id')
                ->pluck('amount', 'cash_count_id');
            $result = [];
            $store_ids = $stores->pluck('name', 'id')->toArray();
            foreach ($cashcounts as $cashcount) {
                $accountName = isset($store_ids[$cashcount->account_id]) ? $store_ids[$cashcount->account_id] : $cashcount->account_id;
                $creditRefund = isset($creditRefunds[$cashcount->id]) ? $creditRefunds[$cashcount->id] : 0;
                if (isset($result[$accountName]) == false) {
                    $result[$accountName] = $columns;
                }
                if (isset($result[$accountName][$cashcount->cash_count_date]) !== false) {
                    $result[$accountName][$cashcount->cash_count_date] =
                        $cashcount->total_sales - $cashcount->refunds_cash - $creditRefund;
                }
            }
            $columns = array_merge(['tiendas'], array_keys($columns));
            $name = 'reporte_ventas_netas_cierre_caja_' . str_replace('-', '_', $current_date) . '.csv';
            $fp = fopen($name, 'w');
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($result as $account => $dates) {
                $_result = array_merge([$account], $dates);
                fputcsv($fp, (array)$_result, ';');
            }
            fclose($fp);
            return redirect('/' . $name);
        }
        return view('advancereports.cash_count_net_sales', ['stores' => $stores]);
    }

    public function transferItemsAcceptedByTimePeriod(Request $request)
    {
        $data = $request->all();
        $result = null;
        $stores = Account::select('name', 'id')->get();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store_id = $data['store'];
            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');

            $transfers = collect(
                DB::table('transfers')
                    ->where(function ($query) use ($store_id) {
                        $query->where('from_account_id', $store_id)
                            ->orWhere('to_account_id', $store_id);
                    })
                    ->whereDate('created_at', '>=', $from_date)
                    ->whereDate('created_at', '<=', $to_date)
                    ->select([
                        'id',
                        'from_account_id',
                        'to_account_id',
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_at')
                    ])
                    ->get()
            );

            $itemsTransfers = collect(
                DB::table('transfer_items')
                    ->whereIn('transfer_id', $transfers->pluck('id')->toArray())
                    ->where('complete', 1)
                    ->select(['transfer_id', 'product_id', 'product_key', 'notes', 'qty'])
                    ->get()
            );
            $transfers = $transfers->keyBy('id');
            $products = collect(
                DB::table('products')
                    ->whereIn('id', $itemsTransfers->pluck('product_id')->toArray())
                    ->orWhere(function ($query) use ($store_id, $itemsTransfers) {
                        $query->where('account_id', $store_id)
                            ->whereIn('product_key', $itemsTransfers->pluck('product_key')->toArray());
                    })
                    ->select(['id', 'product_key', 'cost', 'price', 'wholesale_price', 'special_price'])
                    ->get()
            )->keyBy('product_key');
            $stores_pluck = $stores->pluck('name', 'id');
            $result = [];
            $trueCost = Auth::user()->realUser()->_can('cost');
            foreach ($itemsTransfers as $item) {
                $result[$item->transfer_id][] = [
                    'created_at' => $transfers[$item->transfer_id]->created_at,
                    'product_key' => $item->product_key,
                    'type' => ($transfers[$item->transfer_id]->from_account_id == $store_id) ? 'salida' : 'entrada',
                    'from_account_id' => $stores_pluck[$transfers[$item->transfer_id]->from_account_id],
                    'to_account_id' => $stores_pluck[$transfers[$item->transfer_id]->to_account_id],
                    'notes' => $item->notes,
                    'qty' => $item->qty,
                    'cost' => (isset($products[$item->product_key]) && $trueCost) ? $products[$item->product_key]->cost : 0,
                    'price' => isset($products[$item->product_key]) ? $products[$item->product_key]->price : 0,
                    'wholesale_price' => isset($products[$item->product_key]) ? $products[$item->product_key]->wholesale_price : 0,
                    'special_price' => isset($products[$item->product_key]) ? $products[$item->product_key]->special_price : 0
                ];
            }
            $columns = ['transferencia', 'fecha de creacion', 'producto', 'tipo', 'desde', 'hasta', 'notas', 'cantidad', 'costo', 'precio', 'precio_mayoreo', 'precio_especial'];
            $name = 'reporte_productos_transferencia_periodo_' . str_replace('-', '_', $current_date) . '.csv';
            $fp = fopen($name, 'w');
            fputcsv($fp, CSV_SEPARATOR, ';');
            fputcsv($fp, $columns, ';');
            foreach ($result as $transferId => $items) {
                foreach ($items as $item) {
                    $_result = array_merge([$transferId], $item);
                    fputcsv($fp, (array)$_result, ';');
                }
            }
            fclose($fp);
            return redirect('/' . $name);
        }
        return view('advancereports.transfer_items_accepted_by_time_period', ['stores' => $stores]);
    }

    public function UpdatedDataProductsAccount()
    {
        $reportProcess = ReportProcess::where('report', 'updated_data_products_account_to_warehouse')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.updated_data_products_account_to_warehouse', ['reportProcess' => $reportProcess]);
    }
    public function ProductRelations()
    {
        $reportProcess = ReportProcess::where('report', 'process_product_relations_accounts_count')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.process_product_relations_accounts_count', ['reportProcess' => $reportProcess]);
    }
    public function CountTotalRelationId()
    {
        $reportProcess = ReportProcess::where('report', 'process_count_total_relation_id')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.process_count_total_relation_id', ['reportProcess' => $reportProcess]);
    }
    public function CountTotalProductKey()
    {
        $reportProcess = ReportProcess::where('report', 'process_count_total_product_key')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.process_count_total_product_key', ['reportProcess' => $reportProcess]);
    }
    public function ExportErrorReport(Request $request, $id)
    {
        if (isset($id)) {
            $reportProcess = ReportProcess::where('id', $id)->select('exception')->first();
            if (isset($reportProcess->exception)) {
                $exceptions = explode('*--*', $reportProcess->exception);
                $filePath = public_path('reporte_error_job.csv');
                $fp = fopen($filePath, 'w');
                foreach ($exceptions as $data) {
                    if (isset($data) && trim($data) !== '') {
                        fputcsv($fp, [$data], ';');
                    }
                }
                fclose($fp);
                return response()->download($filePath)->deleteFileAfterSend(true);
            }
        }
        Session::flash('message', "No a seleccionado ningun proceso con error");
        return redirect()->back();
    }

    public function oldPriceProductDate(Request $request)
    {
        $data = $request->all();
        $result = null;
        $stores = Account::select('name', 'id')->get();
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $store_id = (isset($data['store']) && (int)$data['store'] > 0) ? (int)$data['store'] : null;
            $end = Carbon::parse($from_date)->endOfMonth()->format('Y-m-d');

            $products = DB::table('products')
                ->where('account_id', $store_id)
                ->select(['product_key', 'notes', 'qty', 'price', 'wholesale_price', 'special_price'])
                ->get();
            $productKeys = collect($products)->pluck('product_key')->toArray();
            $stockEntries = DB::table('stock_entries')->where('account_id', $store_id)
                ->whereIn('product_key', $productKeys)
                ->whereDate('created_at', '<=', $end)
                ->whereNotNull('wholesale_price_after')
                ->whereNotNull('special_price_after')
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('wholesale_price_after', '>', 0)
                            ->where('special_price_after', '>', 0);
                    })->where('wholesale_price_after', '>', 0)
                        ->orWhere('special_price_after', '>', 0);
                })
                ->select(['product_key', 'wholesale_price_after', 'special_price_after', 'cost_after', 'qty_after', 'created_at'])
                ->latest('created_at')
                ->groupBy('product_key')
                ->get();
            $stockEntries = collect($stockEntries)->keyBy('product_key')->toArray();

            $tracking = DB::table('products_tracking')
                ->where(function ($query) use ($store_id) {
                    $query->where('original_account_id', $store_id)
                        ->orWhere('final_account_id', $store_id);
                })
                ->where(function ($query) {
                    $query->where('final_quantity_after', '>', 0)
                        ->orWhere('final_cost', '>', 0);
                })
                ->whereIn('product_key', $productKeys)
                ->whereDate('created_at', '<=', $end)
                ->select(['product_key', 'final_quantity_after', 'final_cost', 'created_at'])
                ->latest('created_at')
                ->groupBy('product_key')
                ->get();
            $tracking = collect($tracking)->keyBy('product_key')->toArray();



            $filePath = public_path('reporte_old_price_product_date.csv');
            $fp = fopen($filePath, 'w');
            $columns = [
                "Codigo",
                "Producto",
                "Cantidad En Inventario",
                "Precio En Inventario",
                "Precio Mayorista En Inventario",
                "Precio Especial En Inventario",

                'Existe en tracking',
                'cantidad En tracking',
                'costo en tracking',
                'fecha de tracking',

                "Existe en Stock",
                'Cant. Modificada En Stock',
                'Costo Modificada En Stock',
                "Precio Modificada Mayorista En Stock",
                "Precio Modificada Especial En Stock",
                "Fecha de cambio En Stock",
            ];
            fputcsv($fp, $columns, ';');
            foreach ($products as $product) {
                $data = [
                    'product_key' => $product->product_key,
                    'notes' => $product->notes,
                    'qty' => $product->qty,
                    'price' => $product->price,
                    'wholesale_price' => $product->wholesale_price,
                    'special_price' => $product->special_price,

                    'isset_tracking' => isset($tracking[$product->product_key]) ? 'Si' : 'No',
                    'qty_tracking' => isset($tracking[$product->product_key]) ? $tracking[$product->product_key]->final_quantity_after : '',
                    'cost_tracking' => isset($tracking[$product->product_key]) ? $tracking[$product->product_key]->final_cost : '',
                    'created_at_tracking' => isset($tracking[$product->product_key]) ? $tracking[$product->product_key]->created_at : '',

                    'isset_stock' => isset($stockEntries[$product->product_key]) ? 'Si' : 'No',
                    'qty_stock' => isset($stockEntries[$product->product_key]) ? $stockEntries[$product->product_key]->qty_after : '',
                    'cost_stock' => isset($stockEntries[$product->product_key]) ? $stockEntries[$product->product_key]->cost_after : '',
                    'wholesale_price_after' => isset($stockEntries[$product->product_key]) ? $stockEntries[$product->product_key]->wholesale_price_after : '',
                    'special_price_after' => isset($stockEntries[$product->product_key]) ? $stockEntries[$product->product_key]->special_price_after : '',
                    'created_at' => isset($stockEntries[$product->product_key]) ? $stockEntries[$product->product_key]->created_at : '',
                ];
                fputcsv($fp, $data, ';');
            }
            fclose($fp);
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        return view('advancereports.old_price_product_date', ['stores' => $stores]);
    }

    public function stock_in_stores(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get()->keyBy('id');

        if (count($data) > 0) {
            $accounts = $data['store'] ? [$data['store']] : $stores->pluck('id')->toArray();
            $columns = [
                'TIENDA',
                'EMPRESA',
                'RTN',
                'QTY PRODUCTOS',
                'INVENTARIO $',
                'NUMERO TELEFONICO',
                'DIRECCION',
                'FECHA CIERRE'
            ];

            $currentStores = $accounts;
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'total_inventario_tiendas_' . $currentDate[0] . $currentTime . '.csv';

            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');

            fwrite($fp, $bom);
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = count($currentStores);

            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'total_inventario_tiendas';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            dispatch((new ReportStockInStores($nameFile, $reportProcessId, $currentStores))->delay(60));
        }
        $reportProcess = ReportProcess::where('report', 'total_inventario_tiendas')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.stock_in_stores', ['reportProcess' => $reportProcess,  'stores' => $stores]);
    }

    public function commission_old_products(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get()->keyBy('id');
        if (count($data) > 0) {
            $accounts = $data['store'] ? [$data['store']] : $stores->pluck('id')->toArray();
            $columns = [
                'Tienda',
                'Vendedor',
                'Rango fecha',
                'Product key',
                'Fecha Venta',
                'Numero Factura',
                'Cantidad',
                'Precio',
                'Total Precio',
                'Comision 4%'
            ];

            $currentStores = $accounts;


            $from_date = $data['from_date'];
            $to_date = $data['to_date'];


            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'commission_old_products_' . $currentDate[0] . $currentTime . '.csv';

            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');

            fwrite($fp, $bom);
            fputcsv($fp, $columns, ';');
            fclose($fp);
            $rows = count($currentStores);

            $reportProcess = new ReportProcess;
            $reportProcess->file = $nameFile;
            $reportProcess->report = 'commission_old_products';
            $reportProcess->status = 0;
            $reportProcess->count_rows = 0;
            $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));
            $reportProcess->save();
            $reportProcessId = $reportProcess->id;
            if ($rows == 1) {
                dispatch((new ReportCommissionOldProducts($nameFile, $reportProcessId, $currentStores, $from_date, $to_date))->delay(60));
            } else {
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores) {
                    dispatch((new ReportCommissionOldProducts($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date))->delay(60 * $count));
                    $count = $count + 1;
                };
            };
        }
        $reportProcess = ReportProcess::where('report', 'commission_old_products')->orderBy('id', 'DESC')->take(30)->get();
        return view('advancereports.commission_old_products', ['reportProcess' => $reportProcess, 'stores' => $stores]);
    }

    public function invoices_deleted(Request $request)
    {
        $accounts = Account::where('exclude', 0)->get()->keyBy('id');

        $data = $request->all();
        $result = null;
        if (count($data) > 0) {
            $account_id = $data['account_id'] ? [$data['account_id']] : $accounts->pluck('id')->toArray();
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $items = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->join('clients', 'clients.id', '=', 'invoices.client_id')
                ->whereIn('accounts.id', $account_id)
                ->whereNotNull('invoices.deleted_at')
                ->whereNull('invoice_items.deleted_at')
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.deleted_at', '>=', $from_date)
                ->whereDate('invoices.deleted_at', '<=', $to_date)
                ->select(
                    'invoice_items.product_key',
                    'invoice_items.notes',
                    'invoices.deleted_at',
                    'invoice_items.qty',
                    'invoice_items.cost',
                    'invoices.invoice_number',
                    'invoices.invoice_date',
                    'accounts.name as account',
                    'users.first_name',
                    'users.last_name',
                    'clients.name as client',
                    'invoices.employee_id',
                    'accounts.id as account_id'
                )
                ->orderBy('invoices.invoice_date', 'DESC')
                ->get();
            $employees = Employee::where('is_seller', 1)->get()->keyBy('id');
            $result = [];
            foreach ($items as $item) {
                $products = Product::where('account_id', $item->account_id)->where('product_key', $item->product_key)->value('qty');

                $result[] = [
                    'account' => $item->account,
                    'invoice_number' => $item->invoice_number,
                    'product_key' => $item->product_key,
                    'notes' => $item->notes,
                    'user' => $item->first_name . " " . $item->last_name,
                    'employee' => isset($employees[$item->employee_id]) ? $employees[$item->employee_id]->first_name . " " . $employees[$item->employee_id]->last_name : null,
                    'client' => $item->client,
                    'date_deleted' => $item->deleted_at,
                    'date' => $item->invoice_date,
                    'qty' => $item->qty,
                    'cost' => $item->cost,
                    'qtyInhouse' => $products
                ];
            }
        }
        if (isset($data['export']) && $data['export'] == 1) {
            return $this->export('invoices_deleted', $result);
        }
        return view('advancereports.invoices_deleted', ['result' => $result, 'accounts' => $accounts, 'data' => $data]);
    }

    public function SubcategoryGroupByCategory(Request $request)
    {
        $data = $request->all();
        $stores = Account::where('exclude', 0)->get()->keyBy('id');
        if (count($data) > 0) {
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];
            $to_date = date('Y-m-d', strtotime($to_date . ' + 1 days'));
            $store = (isset($data['store']) && $data['store'] !== 'all') ? $data['store'] : null;
            $categories = Category::get()->keyBy('category_id')->toArray();
            $subCategories = SubCategory::get()->keyBy('id')->toArray();
            $rotations = Rotation::get()->keyBy('id')->toArray();

            $startDate  = Carbon::now()->subMonths(6);
            $endDate = Carbon::now();

            $itemsAccount = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->select([
                    'invoice_items.qty',
                    'products.cost',
                    'products.category_id',
                    'products.sub_category_id'
                ])
                ->where('invoices.invoice_type_id', 1)
                ->where('invoices.invoice_date', '>=', $startDate)
                ->where('invoices.invoice_date', '<', $endDate)
                ->whereNull('invoice_items.deleted_at');
            if ($store !== null) {
                $itemsAccount = $itemsAccount->where('invoices.account_id', $store);
            }
            $itemsAccount = $itemsAccount->get();

            $qtySalesProducts = [];
            $salesProducts = [];
            foreach ($itemsAccount as $item) {
                $categoryId = (isset($item->category_id) && $item->category_id > 0) ? $item->category_id : 0;
                $subCategoryId = (isset($item->sub_category_id) && $item->sub_category_id > 0) ? $item->sub_category_id : 0;
                if (!isset($qtySalesProducts[$categoryId])) {
                    $qtySalesProducts[$categoryId] = [];
                }
                if (!isset($qtySalesProducts[$categoryId][$subCategoryId])) {
                    $qtySalesProducts[$categoryId][$subCategoryId] = 0;
                }
                if (!isset($salesProducts[$categoryId])) {
                    $salesProducts[$categoryId] = [];
                }
                if (!isset($salesProducts[$categoryId][$subCategoryId])) {
                    $salesProducts[$categoryId][$subCategoryId] = 0;
                }
                $qtySalesProducts[$categoryId][$subCategoryId] += $item->qty;
                $salesProducts[$categoryId][$subCategoryId] += $item->qty + $item->cost;
            }

            $invoices = [];

            $invoices = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select(
                    'invoice_items.qty',
                    'invoice_items.cost',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.rotation_id'
                )
                ->where('accounts.exclude', 0)
                ->whereNull('invoice_items.deleted_at')
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.created_at', '>=', $from_date)
                ->whereDate('invoices.created_at', '<=', $to_date)
                ->orderBy('category_id');
            if ($store !== null) {
                $invoices = $invoices->where('invoices.account_id', $store);
            }
            $invoices = $invoices->get();

            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');
            $columns = [
                'CATEGORIA',
                'CANTIDAD',
                'TOTAL',
                'SUBCATEGORIA',
                'CANTIDAD',
                'TOTAL',
                'PROM. CANT. VEN. SUBCAT. ULT. 6 MESES',
                'PROM. LEMP. VEN. SUBCAT. ULT. 6 MESES'
            ];
            foreach ($rotations as $key => $value) {
                $columns[] = 'ROTACION ' . $value['name'];
            }
            $columns[] = 'SIN ROTACION';
            $name = 'sub_categoria_agrupada_por_categoria' . str_replace('-', '_', $current_date) . '.csv';
            $result = [];
            foreach ($invoices as $item) {
                $categoryId = (isset($item->category_id) && $item->category_id > 0) ? $item->category_id : 0;
                $category = ($categoryId > 0) ? $categories[$categoryId]['name'] : 'Por Definir';
                if (isset($result[$category]) === false) {
                    $result[$category] = [
                        'qty' => 0,
                        'total' => 0
                    ];
                }
                $result[$category]['qty'] += intval($item->qty);
                $result[$category]['total'] += (intval($item->qty) * floatval($item->cost));
                $subCategoryId = (isset($item->sub_category_id) && $item->sub_category_id > 0) ? $item->sub_category_id : 0;
                $subCategory = ($subCategoryId > 0) ? $subCategories[$subCategoryId]['name'] : 'Por Definir';
                if (isset($result[$category]['sub_categories']) === false) {
                    $result[$category]['sub_categories'] = [];
                }
                if (isset($result[$category]['sub_categories'][$subCategory]) === false) {
                    $result[$category]['sub_categories'][$subCategory] = [
                        'qty' => 0,
                        'total' => 0
                    ];
                }
                $result[$category]['sub_categories'][$subCategory]['qty'] += intval($item->qty);
                $result[$category]['sub_categories'][$subCategory]['total'] += (intval($item->qty) * floatval($item->cost));

                $result[$category]['sub_categories'][$subCategory]['qty_sales_products'] =
                    isset($qtySalesProducts[$categoryId][$subCategoryId]) ?
                    intval(ceil($qtySalesProducts[$categoryId][$subCategoryId] / 6)) : 0;
                $result[$category]['sub_categories'][$subCategory]['sales_products'] =
                    isset($salesProducts[$categoryId][$subCategoryId]) ?
                    intval(ceil($salesProducts[$categoryId][$subCategoryId] / 6)) : 0;

                $rotationId = (isset($item->rotation_id) && $item->rotation_id > 0) ? $item->rotation_id : 0;
                $notIssetRotation = false;
                foreach ($rotations as $key => $value) {
                    if (isset($result[$category]['sub_categories'][$subCategory][$key]) === false) {
                        $result[$category]['sub_categories'][$subCategory][$key] = 0;
                    }
                    if ($rotationId === $key) {
                        $result[$category]['sub_categories'][$subCategory][$key] += intval($item->qty);
                        $notIssetRotation = true;
                    }
                }
                if ($notIssetRotation === false) {
                    if (isset($result[$category]['sub_categories'][$subCategory]['rotation_null']) === false) {
                        $result[$category]['sub_categories'][$subCategory]['rotation_null'] = 0;
                    }
                    $result[$category]['sub_categories'][$subCategory]['rotation_null'] += intval($item->qty);
                }
            }
            $fp = fopen($name, 'w');
            fputcsv($fp, $columns, ';');
            foreach ($result as $key => $value) {
                foreach ($value['sub_categories'] as $key2 => $value2) {
                    $data = [
                        $key,
                        $value['qty'],
                        $value['total'],
                        $key2,
                        $value2['qty'],
                        $value2['total'],
                        $value2['qty_sales_products'],
                        $value2['sales_products']
                    ];
                    foreach ($rotations as $key3 => $value3) {
                        if (isset($value2[$key3])) {
                            $data[] = $value2[$key3];
                        } else {
                            $data[] = 0;
                        }
                    }
                    if (isset($value2['rotation_null'])) {
                        $data[] = $value2['rotation_null'];
                    } else {
                        $data[] = 0;
                    }
                    fputcsv($fp, $data, ';');
                }
            }
            fclose($fp);
            return redirect('/' . $name);
        }
        return view('advancereports.subcategory_group_by_category', ['stores' => $stores]);
    }

    public function salesSubcategoryByMonth(Request $request)
    {
        $data = $request->all();
        $accounts = Account::where('exclude', 0)->get()->keyBy('id');
        if (count($data) > 0) {
            $withTracking = (isset($data['with_tracking']) && (int) $data['with_tracking'] > 0) ? true : false;

            $monthAgo = $data['month_ago'] == "null" ? 1 : $data['month_ago'];
            $store = (isset($data['store']) && $data['store'] !== 'all') ? $data['store'] : null;

            $date = new \Datetime();
            $current_date = $date->format('Y-m-d');

            $from_date = $data['month_ago'] == "null" ? $data['from_date'] : date('Y-m-01', strtotime(date('Y-m-d') . " - {$monthAgo} months"));
            $start = Carbon::parse($from_date)->startOfMonth();
            $from_date = $start->format('Y-m-d');

            $to_date = $data['month_ago'] == "null" ? $data['to_date'] :  date('Y-m-t', strtotime($current_date));
            $end = Carbon::parse($to_date)->endOfMonth();
            $to_date = $end->format('Y-m-d');

            $period = new CarbonPeriod($start, '1 month', $end);
            $columns = [];
            $data_tracking = [];
            foreach ($period as $dt) {
                $_date = $dt->format('Y-m');
                $columns[$_date] = [
                    'qty' => 0,
                    'total' => 0
                ];
                if ($withTracking === true) {
                    $columns[$_date]['qty_tracking'] = 0;
                    $monthEnd = Carbon::parse($to_date)->endOfMonth()->format('Y-m-d');
                    $tracking = DB::table('products_tracking')
                        ->select(
                            'products_tracking.product_id',
                            'products.category_id',
                            'products.sub_category_id',
                            DB::raw('DATE_FORMAT(products_tracking.created_at, "%Y-%m-%d") as tracking_created_at'),
                            'products_tracking.original_account_id',
                            'products_tracking.final_account_id',
                            'products_tracking.original_quantity_after',
                            'products_tracking.final_quantity_after',
                            DB::raw('DATE_FORMAT(products_tracking.created_at, "%Y-%m") as tracking_month'),
                            'products_tracking.product_key'
                        )
                        ->join('products', 'products_tracking.product_id', '=', 'products.id')
                        ->whereDate('products_tracking.created_at', '<=', $monthEnd)
                        ->groupBy('products_tracking.product_key', 'products_tracking.original_account_id')
                        ->havingRaw('MAX(products_tracking.created_at)');
                    if ($store !== null) {
                        $tracking = $tracking->where(function ($query) use ($store) {
                            $query->where('products_tracking.original_account_id', $store)
                                ->orWhere('products_tracking.final_account_id', $store);
                        });
                    }
                    $tracking = $tracking->get();
                    foreach ($tracking as $value) {
                        $tracking_month = $value->tracking_month;

                        $original_account_id = $value->original_account_id;
                        $final_account_id = $value->final_account_id;

                        if (!isset($data_tracking[$tracking_month])) {
                            $data_tracking[$tracking_month] = [];
                        }

                        if ($original_account_id == $final_account_id) {
                            if (!isset($data_tracking[$tracking_month][$final_account_id])) {
                                $data_tracking[$tracking_month][$final_account_id] = [];
                            }
                            if (!isset($data_tracking[$tracking_month][$final_account_id][$value->product_key])) {
                                $data_tracking[$tracking_month][$final_account_id][$value->product_key] = [
                                    'product_key' => $value->product_key,
                                    'category_id' => $value->category_id,
                                    'sub_category_id' => $value->sub_category_id,
                                    'qty' => $value->original_quantity_after,
                                    'tracking_created_at' => $value->tracking_created_at
                                ];
                            } else {
                                $timestamp1 = strtotime($value->tracking_created_at);
                                $timestamp2 = strtotime($data_tracking[$tracking_month][$final_account_id][$value->product_key]['tracking_created_at']);

                                if ($timestamp1 > $timestamp2) {
                                    $data_tracking[$tracking_month][$final_account_id][$value->product_key] = [
                                        'product_key' => $value->product_key,
                                        'category_id' => $value->category_id,
                                        'sub_category_id' => $value->sub_category_id,
                                        'qty' => $value->original_quantity_after,
                                        'tracking_created_at' => $value->tracking_created_at
                                    ];
                                }
                            }
                        } else {
                            if (!isset($data_tracking[$tracking_month][$original_account_id])) {
                                $data_tracking[$tracking_month][$original_account_id] = [];
                            }
                            if (!isset($data_tracking[$tracking_month][$original_account_id][$value->product_key])) {
                                $data_tracking[$tracking_month][$original_account_id][$value->product_key] = [
                                    'product_key' => $value->product_key,
                                    'category_id' => $value->category_id,
                                    'sub_category_id' => $value->sub_category_id,
                                    'qty' => $value->original_quantity_after,
                                    'tracking_created_at' => $value->tracking_created_at
                                ];
                            } else {
                                $timestamp1 = strtotime($value->tracking_created_at);
                                $timestamp2 = strtotime($data_tracking[$tracking_month][$original_account_id][$value->product_key]['tracking_created_at']);

                                if ($timestamp1 > $timestamp2) {
                                    $data_tracking[$tracking_month][$original_account_id][$value->product_key] = [
                                        'product_key' => $value->product_key,
                                        'category_id' => $value->category_id,
                                        'sub_category_id' => $value->sub_category_id,
                                        'qty' => $value->original_quantity_after,
                                        'tracking_created_at' => $value->tracking_created_at
                                    ];
                                }
                            }

                            // aqui
                            if (!isset($data_tracking[$tracking_month][$final_account_id])) {
                                $data_tracking[$tracking_month][$final_account_id] = [];
                            }
                            if (!isset($data_tracking[$tracking_month][$final_account_id][$value->product_key])) {
                                $data_tracking[$tracking_month][$final_account_id][$value->product_key] = [
                                    'product_key' => $value->product_key,
                                    'category_id' => $value->category_id,
                                    'sub_category_id' => $value->sub_category_id,
                                    'qty' => $value->original_quantity_after,
                                    'tracking_created_at' => $value->tracking_created_at
                                ];
                            } else {
                                $timestamp1 = strtotime($value->tracking_created_at);
                                $timestamp2 = strtotime($data_tracking[$tracking_month][$final_account_id][$value->product_key]['tracking_created_at']);

                                if ($timestamp1 > $timestamp2) {
                                    $data_tracking[$tracking_month][$final_account_id][$value->product_key] = [
                                        'product_key' => $value->product_key,
                                        'category_id' => $value->category_id,
                                        'sub_category_id' => $value->sub_category_id,
                                        'qty' => $value->original_quantity_after,
                                        'tracking_created_at' => $value->tracking_created_at
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            $columns['total'] = [
                'qty' => 0,
                'total' => 0,
                'qty_actual' => 0
            ];
            if ($withTracking === true) {
                $categoryTracking = [];
                foreach ($data_tracking as $key => $value) {
                    if (!isset($categoryTracking[$key])) {
                        $categoryTracking[$key] = [];
                    }
                    foreach ($value as $k => $v) {
                        foreach ($v as $key2 => $item) {
                            $categoryId = (isset($item['category_id']) && $item['category_id'] > 0) ? $item['category_id'] : 0;
                            $subCategoryId = (isset($item['sub_category_id']) && $item['sub_category_id'] > 0) ? $item['sub_category_id'] : 0;

                            if (!isset($categoryTracking[$key][$categoryId])) {
                                $categoryTracking[$key][$categoryId] = [];
                            }
                            if (!isset($categoryTracking[$key][$categoryId][$subCategoryId])) {
                                $categoryTracking[$key][$categoryId][$subCategoryId] = 0;
                            }
                            $categoryTracking[$key][$categoryId][$subCategoryId] += $item['qty'];
                        }
                    }
                }
            }
            $invoices = DB::table('invoices')
                ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->join('accounts', 'accounts.id', '=', 'invoices.account_id')
                ->select(
                    'invoice_items.qty',
                    'invoice_items.cost',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.rotation_id',
                    DB::raw('DATE_FORMAT(invoices.invoice_date, "%Y-%m") as invoice_month')
                )
                ->where('accounts.exclude', 0)
                ->whereNull('invoice_items.deleted_at')
                ->where('invoices.invoice_type_id', 1)
                ->whereDate('invoices.created_at', '>=', $from_date)
                ->whereDate('invoices.created_at', '<=', $to_date)
                ->orderBy('category_id');
            if ($store !== null) {
                $invoices = $invoices->where('invoices.account_id', $store);
            }
            $invoices = $invoices->get();

            $products = DB::table('products')
                ->join('accounts', 'accounts.id', '=', 'products.account_id')
                ->select(
                    'products.product_key',
                    'products.account_id',
                    'products.category_id',
                    'products.sub_category_id',
                    DB::raw('SUM(products.qty) as qty_global')
                )
                ->groupBy('products.product_key')
                ->where('accounts.exclude', 0);
            if ($store !== null) {
                $products = $products->where('products.account_id', $store);
            }
            $products = $products->get();
            $qty_actual = [];
            foreach ($products as $product) {
                $categoryId = (isset($product->category_id) && $product->category_id > 0) ? $product->category_id : 0;
                $subCategoryId = (isset($product->sub_category_id) && $product->sub_category_id > 0) ? $product->sub_category_id : 0;
                if (!isset($qty_actual[$categoryId])) {
                    $qty_actual[$categoryId] = [];
                }
                if (!isset($qty_actual[$categoryId][$subCategoryId])) {
                    $qty_actual[$categoryId][$subCategoryId] = 0;
                }
                $qty_actual[$categoryId][$subCategoryId] += isset($product->qty_global) ? $product->qty_global : 0;
            }

            $categories = Category::get()->keyBy('category_id')->toArray();
            $subCategories = SubCategory::get()->keyBy('id')->toArray();

            $name = 'ventas_sub_categoria_por_mes' . str_replace('-', '_', $current_date) . '.csv';
            $result = [];
            foreach ($invoices as $item) {
                $categoryId = (isset($item->category_id) && $item->category_id > 0) ? $item->category_id : 0;
                $category = ($categoryId > 0) ? $categories[$categoryId]['name'] : 'Por Definir';
                if (isset($result[$category]) === false) {
                    $result[$category] = [];
                }
                $subCategoryId = (isset($item->sub_category_id) && $item->sub_category_id > 0) ? $item->sub_category_id : 0;
                $subCategory = ($subCategoryId > 0) ? $subCategories[$subCategoryId]['name'] : 'Por Definir';
                if (isset($result[$category]['sub_categories']) === false) {
                    $result[$category]['sub_categories'] = [];
                }
                if (isset($result[$category]['sub_categories'][$subCategory]) === false) {
                    $result[$category]['sub_categories'][$subCategory] = $columns;
                }

                if (isset($result[$category]['sub_categories'][$subCategory][$item->invoice_month]) === false) {
                    $result[$category]['sub_categories'][$subCategory][$item->invoice_month] = [
                        'qty' => 0,
                        'total' => 0,
                    ];
                    if ($withTracking === true) {
                        $result[$category]['sub_categories'][$subCategory][$item->invoice_month]['tracking_created_at'] = 0;
                    }
                }
                $result[$category]['sub_categories'][$subCategory][$item->invoice_month]['qty'] += intval($item->qty);
                $result[$category]['sub_categories'][$subCategory][$item->invoice_month]['total'] += intval($item->qty) * floatval($item->cost);

                if ($withTracking === true) {
                    if (isset($categoryTracking[$item->invoice_month][$categoryId][$subCategoryId])) {
                        $result[$category]['sub_categories'][$subCategory][$item->invoice_month]['qty_tracking'] = $categoryTracking[$item->invoice_month][$categoryId][$subCategoryId];
                    }
                }

                $result[$category]['sub_categories'][$subCategory]['total']['qty'] += intval($item->qty);
                $result[$category]['sub_categories'][$subCategory]['total']['total'] += intval($item->qty) * floatval($item->cost);

                if (!isset($result[$category]['sub_categories'][$subCategory]['total']['qty_actual'])) {
                    $result[$category]['sub_categories'][$subCategory]['total']['qty_actual'] = 0;
                }

                if ($result[$category]['sub_categories'][$subCategory]['total']['qty_actual'] === 0) {
                    $result[$category]['sub_categories'][$subCategory]['total']['qty_actual'] =
                        isset($qty_actual[$categoryId][$subCategoryId]) ? $qty_actual[$categoryId][$subCategoryId] : 0;
                }
            }
            $fp = fopen($name, 'w');
            $head = ['Categoria', 'Subcategoria'];
            foreach ($columns as $key3 => $value3) {
                $head[] = $key3 . ' Cantidad';
                $head[] = $key3 . ' Total';
                if ($withTracking === true) {
                    if (trim($key3) !== 'total') {
                        $head[] = $key3 . ' Existencia Segun Tracking';
                    }
                }
                if (trim($key3) === 'total') {
                    $head[] = $key3 . ' Existencia Actual';
                }
            }
            fputcsv($fp, $head, ';');
            foreach ($result as $key => $value) {
                foreach ($value['sub_categories'] as $key2 => $value2) {
                    $data = [
                        $key,
                        $key2,
                    ];
                    foreach ($columns as $key3 => $value3) {
                        if (trim($key3) !== 'total') {
                            if (isset($value2[$key3])) {
                                $data[] = isset($value2[$key3]['qty']) ? $value2[$key3]['qty'] : 0;
                                $data[] = isset($value2[$key3]['total']) ? $value2[$key3]['total'] : 0;
                                if ($withTracking === true) {
                                    $data[] = isset($value2[$key3]['qty_tracking']) ? $value2[$key3]['qty_tracking'] : 0;
                                }
                            } else {
                                $data[] = 0;
                                $data[] = 0;
                                if ($withTracking === true) {
                                    $data[] = 0;
                                }
                            }
                        } else {
                            if (isset($value2[$key3])) {
                                $data[] = isset($value2[$key3]['qty']) ? $value2[$key3]['qty'] : 0;
                                $data[] = isset($value2[$key3]['total']) ? $value2[$key3]['total'] : 0;
                                $data[] = isset($value2[$key3]['qty_actual']) ? $value2[$key3]['qty_actual'] : 0;
                            } else {
                                $data[] = 0;
                                $data[] = 0;
                                $data[] = 0;
                            }
                        }
                    }
                    fputcsv($fp, $data, ';');
                }
            }
            fclose($fp);
            return redirect('/' . $name);
        }
        return view('advancereports.sales_subcategory_by_month', ['stores' => $accounts]);
    }

    public function productsWithImages(Request $request)
    {
        $stores = DB::connection('main')->table('accounts')->get();
        $data = $request->all();
        if (count($data) > 0) {
            $account = (isset($data['store']) && trim($data['store']) !== 'all') ? $data['store'] : null;

            $result = DB::connection('main')->table('products')
                ->select('products.product_key',  DB::raw('MAX(products.description) as description'), DB::raw('MAX(products.picture) as picture'))
                ->groupBy('products.product_key');

            if (isset($account)) {
                $result = $result->where('products.account_id', $account);
            }
            $result = $result->get();

            $columns = ['product_key', 'description', 'picture'];
            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ", $currentDate);
            $currentTime = '';
            foreach (explode(":", $currentDate[1]) as $time) {
                $currentTime .= '_' . $time;
            }
            $nameFile = 'products_with_images_' . $currentDate[0] . $currentTime . '.csv';
            $bom = "\xEF\xBB\xBF";
            $file = public_path() . "/" . $nameFile;
            $fp = fopen($file, 'a');
            fwrite($fp, $bom);
            fputcsv($fp, $columns, ';');
            foreach ($result as $item) {
                $data = [
                    $item->product_key,
                    $item->description,
                    $item->picture
                ];
                fputcsv($fp, $data, ';');
            }
            fclose($fp);
            return redirect('/' . $nameFile);
        }
        return view('advancereports.products_with_images', ['stores' => $stores]);
    }
}
