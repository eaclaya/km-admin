<?php

namespace App\Http\Controllers;

use App\Jobs\ReportExportInvoices;
use App\Models\CloningControl;
use App\Models\InvoiceItem;
use App\Models\Invoice;

use App\Models\InvoiceDiscount;
use App\Models\ReportProcess;

use App\Models\Main\Account;
use App\Repositories\ReportProcessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceDiscountController extends Controller
{
    protected $reportProcessRepo;

    public function __construct(ReportProcessRepository $reportProcessRepo)
    {
        $this->reportProcessRepo = $reportProcessRepo;
    }
    public function index(Request $request, $account_id = null)
    {
        $accounts = Account::get();
        $invoiceDiscount = InvoiceDiscount::query();
        if($account_id){
            $invoiceDiscount = $invoiceDiscount->where('account_id', $account_id);
        }
        $invoiceDiscount = $invoiceDiscount->orderBy('created_at', 'desc')->paginate(50);
        return view('invoice_discount.index', ['invoicesDiscount' => $invoiceDiscount, 'accounts' => $accounts]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($invoiceDiscount)
    {
        //
    }

    public function edit($invoiceDiscount)
    {
        //
    }

    public function update(Request $request, $invoiceDiscount)
    {
        //
    }

    public function destroy($invoiceDiscount)
    {
        //
    }
    public function setDiscount(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
        $file = $request->file('csv_file');
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('csv_files', $originalName);
        $field = storage_path('app/'.$path);
        $csv = new \ParseCsv\Csv();
        $csv->encoding('ISO-8859-1','UTF-8');
        $csv->offset = 1;
        $csv->limit = 500;
        $csv->auto($field);
        dd($csv->data);

        return back()->with('success', 'File has been uploaded and processed successfully.');
    }

    public function exportInvoices(Request $request)
    {
        $data = $request->all();
        $accounts = Account::where('accounts.exclude', 0)->select('id','name')->get();
        $name = 'export_invoices';
        if(count($data) > 0){
            $currentAccountId = $data['store'];
            $filter = $data['filter'];

            $currentStores = ($currentAccountId == 'all') ? array_keys($accounts->keyBy('id')->toArray()) : [(int)$currentAccountId];
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];

            $columns = ['invoice_id', 'company', 'account', 'client', 'RTN', 'invoice_number', 'cai', 'invoice_date', 'total'];
            $rows = count($currentStores);

            $data = [
                'name' => $name,
                'columns' => $columns,
                'rows' => $rows
            ];

            $reportProcess = $this->reportProcessRepo->process($data);
            $reportProcessId = $reportProcess->id;

            if($rows == 1){
                dispatch((new ReportExportInvoices($reportProcess->file, $reportProcessId, $currentStores, $from_date, $to_date, $filter))->delay(60));
            }else{
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores){
                    dispatch((new ReportExportInvoices($reportProcess->file, $reportProcessId, $chunkStores, $from_date, $to_date, $filter))->delay(60 * $count));
                    $count = $count+1;
                };
            };

        }
        $dateFromControl = CloningControl::where('model', ENTITY_INVOICE)->where('is_completed',1)->first()->from_date->format('d-m-Y');
        $dateToControl = CloningControl::where('model', ENTITY_INVOICE)->where('is_completed',1)->latest("id")->first()->to_date->format('d-m-Y');

        return view('invoice_discount.export_invoice',
            [
                'accounts' => $accounts,
                'dateFromControl' => $dateFromControl,
                'dateToControl' => $dateToControl,
                'name' => $name
            ]);
    }

    public function finishReport(Request $request)
    {
        $reportProcess = ReportProcess::find($request->id);
        $reportProcess->status = 1;
        $reportProcess->save();
        return redirect()->route('invoice_discount.export_invoice')->with('success', 'Report has been finished successfully.');
    }
}
