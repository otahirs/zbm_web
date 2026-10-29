<?php
test('pdf renders czech text', function () {
    $pdf = Grav\Plugin\Dotace\PdfRenderer::render('<html><body><h1>Žluťoučký kůň úpěl ďábelské ódy</h1></body></html>');
    eq(substr($pdf, 0, 5), '%PDF-');
    ok(strpos($pdf, 'DejaVuSans') !== false, 'DejaVu font embedded');
});
