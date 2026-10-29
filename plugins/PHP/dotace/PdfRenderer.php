<?php
namespace Grav\Plugin\Dotace;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfRenderer
{
    public static function render(string $html): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        // Keep generated *.ufm.json out of the GitSync-tracked vendor directory.
        $options->setFontCache(sys_get_temp_dir());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }
}
