<?php

namespace App\Repositories;

use App\Models\ReportProcess;
use Carbon\Carbon;

class ReportProcessRepository
{
    public function createReportProcess($nameFile, $name, $rows, $chunkLimit): ReportProcess
    {
        $reportProcess = new ReportProcess;
        $reportProcess->file = $nameFile;
        $reportProcess->report = $name;
        $reportProcess->status = 0;
        $reportProcess->count_rows = 0;
        $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / $chunkLimit));

        $reportProcess->save();
        return $reportProcess;
    }

    public function updateReportProcess($reportProcessId): void
    {
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->count_rows = is_null($reportProcess->count_rows) ? 1 : (int)$reportProcess->count_rows + 1;
        $finish = ($reportProcess->count_rows >= $reportProcess->rows) ? true : false;
        if($finish){
            $reportProcess->updated_at = Carbon::now()->toDateTimeString();
            $reportProcess->status = 1;
        }
        $reportProcess->save();
    }

    public function finishReport($reportProcessId): void
    {
        $reportProcess = ReportProcess::find($reportProcessId);
        $reportProcess->status = 1;
        $reportProcess->save();
    }
}
