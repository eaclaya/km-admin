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
        $contentPdf = PDF::loadView($view_pdf, $data);
        $contentPdf->save($newContentPath);

        $pdf = new TcpdfFpdi();
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
        if (file_exists($newContentPath)) {
            unlink($newContentPath);
        }
    }

    public function createFileCsv($nameFile, $columns): void
    {
        $bom = "\xEF\xBB\xBF";
        $file = public_path() . "/" . $nameFile;
        $fp = fopen($file, 'a');
        fwrite($fp, $bom);
        fputcsv($fp, CSV_SEPARATOR, ';');
        fputcsv($fp, $columns, ';');
        fclose($fp);
    }
}
