<?php

declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * PDF Service — Wrapper for Dompdf
 */
final class PDFService
{
    /**
     * Generate PDF from HTML and stream/download it.
     */
    public static function generate(string $html, string $filename = 'document.pdf', bool $download = true): void
    {
        // Increase resource limits for PDF generation
        ini_set('memory_limit', '256M');
        set_time_limit(60);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Times-Roman');
        $options->set('debugKeepTemp', false);
        $options->set('isPhpEnabled', false); // Security: disable PHP execution inside PDF
        $options->set('isFontSubsettingEnabled', true); // Smaller file size

        $dompdf = new Dompdf($options);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($filename, ['Attachment' => $download ? 1 : 0]);
        exit;
    }
}
