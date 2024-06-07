<?php

namespace App\Http\Controllers;

use App\Jobs\ReportExportInvoices;
use App\Models\CloningControl;
use App\Models\InvoiceItem;
use App\Models\Invoice;

use App\Models\InvoiceDiscount;
use App\Models\ReportProcess;

use App\Models\Main\Account;
use Carbon\Carbon;
use Dotenv\Parser\Entry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceDiscountController extends Controller
{
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
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);
        dd($request->file('csv_file'));
        $path = $request->file('csv_file')->store('csv_files');

        $file = fopen(storage_path('app/' . $path), 'r');

        /*while (($data = fgetcsv($file)) !== FALSE) {
            // $data is an array with your columns
            // process your data here
        }*/

        // Close file
        fclose($file);

        return back()->with('success', 'File has been uploaded and processed successfully.');
    }

    public function exportInvoices(Request $request)
    {
        $data = $request->all();
        $accounts = Account::where('accounts.exclude', 0)->select('id','name')->get();
        if(count($data) > 0){
            $currentAccountId = $data['store'];
            $filter = $data['filter'];

            $currentStores = ($currentAccountId == 'all') ? array_keys($accounts->keyBy('id')->toArray()) : [(int)$currentAccountId];
            $from_date = $data['from_date'];
            $to_date = $data['to_date'];

            $columns = ['invoice_id', 'company', 'account', 'client', 'RTN', 'invoice_number', 'cai', 'invoice_date', 'total'];

            $currentDate = Carbon::now()->toDateTimeString();
            $currentDate = explode(" ",$currentDate);
            $currentTime = '';
            foreach (explode(":",$currentDate[1]) as $time) {
                $currentTime .= '_'.$time;
            }
            $nameFile = 'export_invoices_'.$currentDate[0].$currentTime.'.csv';

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

            if($rows == 1){
                dispatch((new ReportExportInvoices($nameFile, $reportProcessId, $currentStores, $from_date, $to_date, $filter))->delay(60));
            }else{
                $count = 1;
                foreach (array_chunk($currentStores, 4) as $chunkStores){
                    dispatch((new ReportExportInvoices($nameFile, $reportProcessId, $chunkStores, $from_date, $to_date, $filter))->delay(60 * $count));
                    $count = $count+1;
                };
            };

        }
        $reportProcess = ReportProcess::where('report','export_invoices')->orderBy('id', 'DESC')->take(30)->get();
        $dateFromControl = CloningControl::where('model', ENTITY_INVOICE)->where('is_completed',1)->first()->from_date->format('d-m-Y');
        $dateToControl = CloningControl::where('model', ENTITY_INVOICE)->where('is_completed',1)->latest("id")->first()->to_date->format('d-m-Y');

        return view('invoice_discount.export_invoice',
            [
                'accounts' => $accounts,
                'reportProcess' => $reportProcess,
                'dateFromControl' => $dateFromControl,
                'dateToControl' => $dateToControl
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
