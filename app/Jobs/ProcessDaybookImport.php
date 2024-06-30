<?php

namespace App\Jobs;

use App\Models\FinanceCatalogueItem;
use App\Models\FinanceDaybookEntry;
use App\Models\Main\Account;
use App\Models\Main\Invoice;
use App\Models\Main\OrganizationCompany;
use App\Repositories\ReportProcessRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Carbon\Carbon;

class ProcessDaybookImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ReportProcessRepository $reportProcessRepo;
    protected int $reportProcessId;
    protected array $currentStores;
    protected $date;
    protected string $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ReportProcessRepository $reportProcessRepo, int $reportProcessId, array $currentStores, $date, string $type)
    {
        $this->reportProcessId = $reportProcessId;
        $this->currentStores = $currentStores;
        $this->date = $date;
        $this->type = $type;
        $this->reportProcessRepo = $reportProcessRepo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportProcessId = $this->reportProcessId;
        $currentStores = $this->currentStores;
        $date = $this->date;
        $type = $this->type;

        $this->processDaybookImport($currentStores, $date, $type);

        $this->reportProcessRepo->updateReportProcess($reportProcessId);
        return;
    }
}
