<?php

namespace App\Http\Controllers;

use App\Jobs\ExportInvoicesPdf;
use App\Jobs\ImportInvoicesDiscount;
use App\Jobs\ReportExportInvoices;
use App\Models\CloningControl;

use App\Models\Main\Account;
use App\Services\ReportProcessServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceDiscountController extends Controller
{
    protected ReportProcessServices $reportProcessServices;

    public function __construct(ReportProcessServices $reportProcessServices)
    {
        $this->reportProcessServices = $reportProcessServices;
    }

    public function index(Request $request, $account_id = null): \Illuminate\Contracts\View\View
    {
        $name = 'export_invoices_pdf';
        return view('invoice_discount.index', ['account_id' => $account_id, 'name' => $name]);
    }

    public function setDiscount(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
        $file = $request->file('csv_file');
        [$rows, $filePath] = $this->reportProcessServices->getFilesServices()->readFileCsv($file, true);
        $chunkLimit = 400;

        $data = [
            'name' => 'import_invoices_discount',
            'chunkLimit' => $chunkLimit,
            'rows' => $rows,
            'name_file' => $filePath
        ];
        $reportProcess = $this->reportProcessServices->processImportCsv($data);
        $reportProcessId = $reportProcess->id;
        $count = 1;
        for ($i=1; $i < $rows; $i=$i+$chunkLimit) {
            $chunk = null;
            $init = ($i == 1) ? 0 : $i;
            $chunk = ['offset' => $init, 'limit' => $i+($chunkLimit-1)];
            dispatch((new ImportInvoicesDiscount($this->reportProcessServices->getRepository(),$reportProcessId,$filePath,$chunk))->delay(30 * $count));
            $count = $count+1;
        }
        return back()->with('success', 'File has been uploaded and processed successfully.');
    }

    public function exportInvoicesCsv(Request $request): \Illuminate\Contracts\View\View
    {
        $data = $request->all();
        $name = 'export_invoices';
        if(count($data) > 0){
            $currentAccountId = $data['store'];
            $filter = $data['filter'];

            $accounts = Account::where('accounts.exclude', 0)->pluck('id')->toArray();
            $currentStores = ($currentAccountId == 'all') ? array_keys($accounts) : [(int)$currentAccountId];
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

            $reportProcess = $this->reportProcessServices->processReportCsv($data);
            $reportProcessId = $reportProcess->id;

            if($rows == 1){
                dispatch((new ReportExportInvoices($this->reportProcessServices->getRepository(),$reportProcess->file, $reportProcessId, $currentStores, $from_date, $to_date, $filter))->delay(60));
            }else{
                $count = 1;
                foreach (array_chunk($currentStores, $chunkLimit) as $chunkStores){
                    dispatch((new ReportExportInvoices($this->reportProcessServices->getRepository(),$reportProcess->file, $reportProcessId, $chunkStores, $from_date, $to_date, $filter))->delay(60 * $count));
                    $count = $count+1;
                };
            };

        }

        $bodySelectAccount = [
            'model' => "App\\Models\\Main\\Account",
            'filters'=> ['name'],
            'columnText'=> ['name'],
            'name' => 'store'
        ];

        return view('invoice_discount.export_invoice',
            [
                'name' => $name,
                'bodySelectAccount' => $bodySelectAccount
            ]);
    }

    public function finishReport(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->reportProcessServices->getRepository()->finishReport($request->id);
        return redirect()->route('invoice_discount.export_invoice')->with('success', 'Report has been finished successfully.');
    }

    public function exportInvoicesPdf(Request $request): \Illuminate\Http\RedirectResponse
    {
        $ids = $request->input('ids');

        if(!is_null($ids)){
            $name = 'export_invoices_pdf';

            $rows = count($ids);
            $chunkLimit = 125;
            $data = [
                'name' => $name,
                'rows' => $rows,
                'chunkLimit' => $chunkLimit
            ];

            $reportProcess = $this->reportProcessServices->processReportPdf($data);
            $reportProcessId = $reportProcess->id;

            if($rows <= $chunkLimit){
                dispatch((new ExportInvoicesPdf($this->reportProcessServices, $reportProcess->file, $reportProcessId, $ids))->delay(60));
            }else{
                $count = 1;
                foreach (array_chunk($ids, $chunkLimit) as $chunkIds){
                    dispatch((new ExportInvoicesPdf($this->reportProcessServices, $reportProcess->file, $reportProcessId, $chunkIds))->delay(60 * $count));
                    $count = $count+1;
                };
            };

        }
        return redirect()->route('invoice_discount.index')->with('success', 'Report has been generated successfully.');
    }
}
