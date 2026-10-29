<?php
test('pdf renders czech text', function () {
    $pdf = Grav\Plugin\Dotace\PdfRenderer::render('<html><body><h1>Žluťoučký kůň úpěl ďábelské ódy</h1></body></html>');
    eq(substr($pdf, 0, 5), '%PDF-');
    ok(strpos($pdf, 'DejaVuSans') !== false, 'DejaVu font embedded');
});

test('render leaves no files in dompdf vendor font dir', function () {
    $dir = dirname(__DIR__, 2) . '/vendor/dompdf/dompdf/lib/fonts';
    $before = glob($dir . '/*');
    Grav\Plugin\Dotace\PdfRenderer::render('<html><body><p>Příliš žluťoučký kůň</p><b>tučně</b></body></html>');
    eq(glob($dir . '/*'), $before);
});
