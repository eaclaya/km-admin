<?php

namespace App\Http\Controllers;

use App\Jobs\ImportInvoicesDiscount;
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
    protected ReportProcessRepository $reportProcessRepo;

    public function __construct(ReportProcessRepository $reportProcessRepo)
    {
        $this->reportProcessRepo = $reportProcessRepo;
    }
    public function index(Request $request, $account_id = null): \Illuminate\Contracts\View\View
    {
        $accounts = Account::get();
        $invoiceDiscount = InvoiceDiscount::query();
        if($account_id){
            $invoiceDiscount = $invoiceDiscount->where('account_id', $account_id);
        }
        $invoiceDiscount = $invoiceDiscount->orderBy('created_at', 'desc')->paginate(50);
        return view('invoice_discount.index', ['invoicesDiscount' => $invoiceDiscount, 'accounts' => $accounts]);
    }

    public function setDiscount(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
        $file = $request->file('csv_file');
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('csv_files', $originalName);
        $filePath = storage_path('app/'.$path);

        $csv = new \ParseCsv\Csv();
        $csv->encoding('ISO-8859-1','UTF-8');
        $csv->auto($filePath);

        $rows = count($csv->data);
        dd($rows);
        $chunkLimit = 400;

        $data = [
            'name' => 'import_invoices_discount',
            'chunkLimit' => $chunkLimit,
            'rows' => $rows,
            'name_file' => $filePath
        ];
        $reportProcess = $this->reportProcessRepo->processImport($data);
        $reportProcessId = $reportProcess->id;
        $count = 1;
        for ($i=1; $i < $rows; $i=$i+$chunkLimit) {
            $chunk = null;
            $init = ($i == 1) ? 0 : $i;
            $chunk = ['offset' => $init, 'limit' => $i+($chunkLimit-1)];
            dispatch((new ImportInvoicesDiscount($this->reportProcessRepo,$reportProcessId,$filePath,$chunk))->delay(30 * $count));
            $count = $count+1;
        }

        return back()->with('success', 'File has been uploaded and processed successfully.');
    }

    public function exportInvoices(Request $request): \Illuminate\Contracts\View\View
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
            $chunkLimit = 4;
            $data = [
                'name' => $name,
                'columns' => $columns,
                'rows' => $rows,
                'chunkLimit' => $chunkLimit
            ];

            $reportProcess = $this->reportProcessRepo->processReport($data);
            $reportProcessId = $reportProcess->id;

            if($rows == 1){
                dispatch((new ReportExportInvoices($this->reportProcessRepo,$reportProcess->file, $reportProcessId, $currentStores, $from_date, $to_date, $filter))->delay(60));
            }else{
                $count = 1;
                foreach (array_chunk($currentStores, $chunkLimit) as $chunkStores){
                    dispatch((new ReportExportInvoices($this->reportProcessRepo,$reportProcess->file, $reportProcessId, $chunkStores, $from_date, $to_date, $filter))->delay(60 * $count));
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

    public function finishReport(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->reportProcessRepo->finishReport($request->id);
        return redirect()->route('invoice_discount.export_invoice')->with('success', 'Report has been finished successfully.');
    }
}
