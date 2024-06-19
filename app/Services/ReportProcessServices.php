<?php

namespace App\Services;

use App\Models\ReportProcess;
use App\Repositories\ReportProcessRepository;
use Carbon\Carbon;

class ReportProcessServices
{
    protected ReportProcessRepository $reportProcessRepo;
    protected FilesServices $filesServices;

    public function __construct(ReportProcessRepository $reportProcessRepo, FilesServices $filesServices)
    {
        $this->reportProcessRepo = $reportProcessRepo;
        $this->filesServices = $filesServices;
    }
    public function processReportCsv($data): ReportProcess
    {
        $name = $data['name'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $chunkLimit = $data['chunkLimit'];

        $nameFile = $this->getNameDataTime($name,'csv');
        $this->filesServices->createFileCsv($nameFile, $columns);
        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }
    public function processImportCsv($data): ReportProcess
    {
        $nameFile = $data['name_file'];
        $name = $data['name'];
        $rows = $data['rows'];
        $chunkLimit = $data['chunkLimit'];
        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
    }
    /*  --------------------    */
    public function processReportPdf($data): ReportProcess
    {
        $rows = $data['rows'];
        $name = $data['name'];
        $chunkLimit = $data['chunkLimit'];
        $nameFile = $this->getNameDataTime($name,'pdf');

        return $this->reportProcessRepo->createReportProcess($nameFile, $name, $rows, $chunkLimit);
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
    public function getRepository(): ReportProcessRepository
    {
        return $this->reportProcessRepo;
    }
    public function getFilesServices(): FilesServices
    {
        return $this->filesServices;
    }
}
