<?php

namespace App\Jobs;

use App\Models\InvoiceDiscount;
use App\Models\ReportProcess;

use App\Repositories\ReportProcessRepository;
use Illuminate\Support\Facades\DB;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Carbon\Carbon;

class ImportInvoicesDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ReportProcessRepository $reportProcessRepo;
    protected int $reportProcessId;
    protected string $filePath;
    protected array $chunk;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ReportProcessRepository $reportProcessRepo, int $reportProcessId, string $filePath, array $chunk)
    {
        $this->reportProcessId = $reportProcessId;
        $this->filePath = $filePath;
        $this->chunk = $chunk;
        $this->reportProcessRepo = $reportProcessRepo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dump('inicio a procesar con la siguiente tienda');
        $reportProcessId = $this->reportProcessId;
        $filePath = $this->filePath;
        $chunk = $this->chunk;

//      ----------------------------
        $this->processImportInvoices($reportProcessId, $filePath, $chunk);
//        -------------------------
        $this->reportProcessRepo->updateReportProcess($reportProcessId);
        return;
    }

    private function processImportInvoices($reportProcessId, $filePath, $chunk): void
    {
        $csv = new \ParseCsv\Csv();
        $csv->encoding('ISO-8859-1','UTF-8');
        $csv->limit = $chunk['limit'];
        $csv->offset = $chunk['offset'];
        $csv->auto($filePath);
        $data = $csv->data;

        foreach ($data as $invoice) {
        }
    }
}
