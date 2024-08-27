<?php

namespace App\Services;

use App\Models\ReportProcess;
use App\Repositories\ReportProcessRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class ReportProcessServices
{
    protected ReportProcessRepository $reportProcessRepo;
    protected FilesServices $filesServices;

    public function __construct(ReportProcessRepository $reportProcessRepo, FilesServices $filesServices)
    {
        $this->reportProcessRepo = $reportProcessRepo;
        $this->filesServices = $filesServices;
    }
    /*  --------------------    */
    public function processReportCsv($data): ReportProcess
    {
        $name = Arr::get($data,'name');
        $columns = Arr::get($data,'columns');
        $rows = Arr::get($data,'rows');
        $chunkLimit = Arr::get($data,'chunkLimit');

        $nameFile = $this->getNameDataTime($name,'csv');
        $this->filesServices->createFileCsv($nameFile, $columns);
        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }
    public function processReportPdf($data): ReportProcess
    {
        $rows = Arr::get($data,'rows');
        $name = Arr::get($data,'name');
        $chunkLimit = Arr::get($data,'chunkLimit');
        $nameFile = $this->getNameDataTime($name,'pdf');

        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }

    /*  --------------------    */
    public function processImportCsv($data): ReportProcess
    {
        $nameFile = Arr::get($data,'name_file');
        $name = Arr::get($data,'name');
        $rows = Arr::get($data,'rows');
        $chunkLimit = Arr::get($data,'chunkLimit');
        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }
    public function processImportDB($data): ReportProcess|false
    {
        $name = Arr::get($data,'name');
        $rows = Arr::get($data,'rows');
        $chunkLimit = Arr::get($data,'chunkLimit');
        $nameFile = $this->getNameData($data);
        return false;
//        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }
    /*  --------------------    */
    public function getNameDataTime($name,$format): string
    {
        $currentDate = Carbon::now()->toDateTimeString();
        $currentDate = explode(" ",$currentDate);
        $currentTime = '';
        foreach (explode(":",$currentDate[1]) as $time) {
            $currentTime .= '_'.$time;
        }
        return  $name.'_'.$currentDate[0].$currentTime.'.'.$format;
    }
    public function getNameData($data): string
    {
        $type = $price = Arr::get($data, 'type');
        $accountName = Arr::get($data,'accountName');
        $date = Arr::get($data,'date');
        $nameFile = Arr::get($data,'name');
        if (isset($type)) {
            $nameFile .= '_'.$type;
        }
        if(isset($accountName)){
            $nameFile .= '_'.$accountName;
        }
        if(isset($date)){
            $nameFile .= '_'.$date;
        }else{
            $nameFile .= '_'.Carbon::now()->toDateString();
        }
        return $nameFile;
    }
    public function getRepository(): ReportProcessRepository
    {
        return $this->reportProcessRepo;
    }
    public function getFilesServices(): FilesServices
    {
        return $this->filesServices;
    }
}
