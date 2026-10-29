<?php
use Grav\Plugin\Dotace\{Seznam, OdsWriter};

test('birth year', function () {
    eq(Seznam::birthYear('9901', 2026), 1999);
    eq(Seznam::birthYear('0712', 2026), 2007);
    eq(Seznam::birthYear('2601', 2026), 2026);
    eq(Seznam::birthYear('12', 2026), null);
    eq(Seznam::birthYear(null, 2026), null);
});
test('rows czech sort and numbering', function () {
    $rows = Seznam::rows([
        ['surname' => 'Šimek', 'name' => 'Petr', 'reg' => '1001'],
        ['surname' => 'Chalupa', 'name' => 'Jan', 'reg' => '0905'],
        ['surname' => 'Čech', 'name' => 'Adam', 'reg' => null],
        ['surname' => 'Hrubý', 'name' => 'Ota', 'reg' => '1102', 'note_internal' => 'X', 'si_chip' => 1],
        ['surname' => 'Cibulka', 'name' => 'Eva', 'reg' => '9912'],
    ], 2026);
    eq(array_column($rows, 1), ['Cibulka Eva', 'Čech Adam', 'Hrubý Ota', 'Chalupa Jan', 'Šimek Petr']);
    eq($rows[0], [1, 'Cibulka Eva', 1999, 'ZBM']);
    eq($rows[1][2], null);
});
test('rows tolerate missing names', function () {
    $rows = Seznam::rows([['reg' => '1001'], ['surname' => 'Novák', 'reg' => '1001']], 2026);
    eq(array_column($rows, 1), ['', 'Novák']);
    eq(array_column($rows, 0), [1, 2]);
});
test('ods structure', function () {
    $bytes = OdsWriter::write('TK <X> - seznam účastníků soustředění', ['#', 'Jméno a Příjmení', 'Rok narození', 'oddíl'], [[1, 'Novák & Syn', 2010, 'ZBM'], [2, 'Bez Roku', null, 'ZBM']]);
    $tmp = tempnam(sys_get_temp_dir(), 'ods');
    try {
        file_put_contents($tmp, $bytes);
        $zip = new ZipArchive();
        ok($zip->open($tmp) === true, 'zip opens');
        eq($zip->getNameIndex(0), 'mimetype');
        eq($zip->getFromName('mimetype'), 'application/vnd.oasis.opendocument.spreadsheet');
        ok($zip->getFromName('META-INF/manifest.xml') !== false);
        $c = $zip->getFromName('content.xml');
        $zip->close();
    } finally {
        unlink($tmp);
    }
    ok(simplexml_load_string($c) !== false, 'valid XML');
    ok(strpos($c, 'TK &lt;X&gt;') !== false && strpos($c, 'Novák &amp; Syn') !== false);
    ok(strpos($c, 'office:value-type="float" office:value="2010"') !== false);
    ok(strpos($c, '<table:table-cell/>') !== false, 'null is empty cell');
});
