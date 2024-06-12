<?php

namespace App\Repositories;

use App\Models\ReportProcess;
use Carbon\Carbon;

class ReportProcessRepository
{
    public function process($data): ReportProcess
    {
        $name = $data['name'];
        $columns = $data['columns'];
        $rows = $data['rows'];

        $currentDate = Carbon::now()->toDateTimeString();
        $currentDate = explode(" ",$currentDate);
        $currentTime = '';
        foreach (explode(":",$currentDate[1]) as $time) {
            $currentTime .= '_'.$time;
        }
        $nameFile = $name.'_'.$currentDate[0].$currentTime.'.csv';

        $bom = "\xEF\xBB\xBF";
        $file = public_path() . "/" . $nameFile;
        $fp = fopen($file, 'a');
        fwrite($fp, $bom);
        fputcsv($fp, CSV_SEPARATOR, ';');
        fputcsv($fp, $columns, ';');
        fclose($fp);

        $reportProcess = new ReportProcess;
        $reportProcess->file = $nameFile;
        $reportProcess->report = $name;
        $reportProcess->status = 0;
        $reportProcess->count_rows = 0;
        $reportProcess->rows = ($rows == 1) ? $rows : intval(ceil($rows / 4));

        $reportProcess->save();
        return $reportProcess;
    }
}
