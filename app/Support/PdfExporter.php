<?php

namespace App\Support;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class PdfExporter
{
    public const TIMEZONE_WIB = 'Asia/Jakarta';

    public static function stream(string $view, array $data, string $filename): Response
    {
        $html = View::make($view, $data)->render();

        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.self::safeFilename($filename).'"',
        ]);
    }

    public static function filterLabel(?string $from, ?string $to, ?string $singleDate = null): string
    {
        if ($singleDate) {
            return 'Per tanggal '.FormatHelper::dateId($singleDate);
        }

        if ($from && $to) {
            return FormatHelper::dateId($from).' s/d '.FormatHelper::dateId($to);
        }

        if ($from) {
            return 'Dari '.FormatHelper::dateId($from);
        }

        if ($to) {
            return 'Sampai '.FormatHelper::dateId($to);
        }

        return 'Semua data';
    }

    public static function generatedAt(): string
    {
        return now()
            ->timezone(self::TIMEZONE_WIB)
            ->locale('id')
            ->translatedFormat('d F Y, H:i').' WIB';
    }

    private static function safeFilename(string $name): string
    {
        $name = preg_replace('/[^\w\-]+/u', '-', $name) ?? 'laporan';

        return trim($name, '-').'.pdf';
    }
}
