<?php

namespace App\Repositories;

use App\Models\ReportProcess;
use Carbon\Carbon;

class ReportProcessRepository
{
    public function processReport($data): ReportProcess
    {
        $name = $data['name'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $chunkLimit = $data['chunkLimit'];

        $nameFile = $this->getNameDataTime($name);
        $this->createFile($nameFile, $columns);
        return $this->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }

    public function processImport($data): ReportProcess
    {
        $nameFile = $data['name_file'];
        $name = $data['name'];
        $rows = $data['rows'];
        $chunkLimit = $data['chunkLimit'];
        return $this->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }

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

    public function getNameDataTime($name): string
    {
        $currentDate = Carbon::now()->toDateTimeString();
        $currentDate = explode(" ",$currentDate);
        $currentTime = '';
        foreach (explode(":",$currentDate[1]) as $time) {
            $currentTime .= '_'.$time;
        }
        return  $name.'_'.$currentDate[0].$currentTime.'.csv';
    }

    public function createFile($nameFile, $columns): void
    {
        $bom = "\xEF\xBB\xBF";
        $file = public_path() . "/" . $nameFile;
        $fp = fopen($file, 'a');
        fwrite($fp, $bom);
        fputcsv($fp, CSV_SEPARATOR, ';');
        fputcsv($fp, $columns, ';');
        fclose($fp);
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
