<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi as TcpdfFpdi;

class FilesServices
{
    public function appendToPdf($data, $filePath, $view_pdf): void
    {
        $directoryPath = 'public/pdf';
        if (!Storage::exists($directoryPath)) {
            Storage::makeDirectory($directoryPath);
        }
        $newContentPath = storage_path('app/public/pdf/new-content.pdf');
        $contentPdf = PDF::loadView($view_pdf, ['dataArr' => $data]);
        $contentPdf->setOptions(['isRemoteEnabled' => true]);
        $contentPdf->save($newContentPath);

        $pdf = new TcpdfFpdi();
        $pdf->setPrintHeader(false);
        if (file_exists($filePath)) {
            $pageCount = $pdf->setSourceFile($filePath);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $pdf->addPage();
                $pdf->useTemplate($templateId);
            }
        }
        $pageCount = $pdf->setSourceFile($newContentPath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->addPage();
            $pdf->useTemplate($templateId);
        }
        $pdf->Output($filePath, 'F');
        /*if (file_exists($newContentPath)) {
            unlink($newContentPath);
        }*/
    }

    public function createFileCsv($nameFile, $columns): void
    {
        $bom = "\xEF\xBB\xBF";
        $file = public_path() . "/" . $nameFile;
        $fp = fopen($file, 'a');
        fwrite($fp, $bom);
        fputcsv($fp, $columns, ';');
        fclose($fp);
    }

    public function readFileCsv($file,$save=false): array
    {
        if($save !== false){
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('csv_files', $originalName);
            $filePath = storage_path('app/'.$path);
        }else{
            $filePath = $file->getRealPath();
        }
        $csv = new \ParseCsv\Csv();
        $csv->encoding('ISO-8859-1','UTF-8');
        $csv->auto($filePath);
        $rows = count($csv->data);
        return [
            $rows,
            ($save !== false)?$filePath:null,
            $csv->data
        ];
    }
}
