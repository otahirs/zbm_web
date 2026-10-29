<?php
use Grav\Plugin\Dotace\{Format, Rozpis};

const DEFAULTS = [
    'meetPlace' => 'Kr. Pole nádraží',
    'transport' => 'auty',
    'accomodation' => 'na postelích',
    'food' => 'polopenze',
    'return' => 'poslední den akce odpoledne',
    'thingsToTake' => 'sportovní oblečení, boty, buzolu, čip, hodinky',
];

test('date range same month', fn() => eq(Format::dateRange('2025-04-17', '2025-04-21'), 'čtvrtek 17. — pondělí 21. dubna 2025'));
test('date range across months', fn() => eq(Format::dateRange('2025-03-31', '2025-04-04'), 'pondělí 31. března — pátek 4. dubna 2025'));
test('date range across years', fn() => eq(Format::dateRange('2025-12-30', '2026-01-02'), 'úterý 30. prosince 2025 — pátek 2. ledna 2026'));
test('date range one day / missing / reversed end', function () {
    eq(Format::dateRange('2025-04-05', '2025-04-05'), 'sobota 5. dubna 2025');
    eq(Format::dateRange('2025-04-05', null), 'sobota 5. dubna 2025');
    eq(Format::dateRange('2025-04-05', ''), 'sobota 5. dubna 2025');
    eq(Format::dateRange('2025-04-05', '2025-04-01'), 'sobota 5. dubna 2025');
});
test('long date', fn() => eq(Format::longDate('2025-04-17'), '17. dubna 2025'));
test('default deadline clamps', function () {
    eq(Format::defaultDeadline('2025-03-31'), '2025-02-28');
    eq(Format::defaultDeadline('2025-04-17'), '2025-03-17');
});
test('program days 5 days', fn() => eq(Rozpis::programDays('2025-04-17', '2025-04-21'), [
    ['day' => 'ČT', 'items' => ['dopo: cesta', 'odpo: první trénink']],
    ['day' => 'PÁ', 'items' => ['dopo: mapová fáze', 'odpo: mapová fáze']],
    ['day' => 'SO', 'items' => ['dopo: mapová fáze']],
    ['day' => 'NE', 'items' => ['dopo: mapová fáze', 'odpo: mapová fáze']],
    ['day' => 'PO', 'items' => ['dopo: mapová fáze', 'odpo: cesta zpět']],
]));
test('program days 1 and 2 days', function () {
    eq(Rozpis::programDays('2025-04-05', null), [['day' => 'SO', 'items' => ['trénink']]]);
    eq(Rozpis::programDays('2025-04-05', '2025-04-06'), [
        ['day' => 'SO', 'items' => ['dopo: cesta', 'odpo: první trénink']],
        ['day' => 'NE', 'items' => ['dopo: mapová fáze', 'odpo: cesta zpět']],
    ]);
});
test('program markdown is nested list', fn() =>
    eq(Rozpis::program('2025-04-05', '2025-04-06'), "- SO\n    - dopo: cesta\n    - odpo: první trénink\n- NE\n    - dopo: mapová fáze\n    - odpo: cesta zpět"));
test('generated program carries days, event program does not', function () {
    $gen = Rozpis::build(['start' => '2025-04-17', 'end' => '2025-04-21'], null, DEFAULTS, 'T')['sections'][5];
    eq(count($gen['days']), 5);
    $own = Rozpis::build(['start' => '2025-04-17', 'program' => 'vlastní'], null, DEFAULTS, 'T')['sections'][5];
    eq($own['value'], 'vlastní');
    ok(!isset($own['days']));
});
test('default things to take become a bullet list, event value kept', function () {
    eq(Rozpis::build(['start' => '2025-04-17'], null, DEFAULTS, 'T')['sections'][6]['value'],
        "- sportovní oblečení\n- boty\n- buzolu\n- čip\n- hodinky");
    eq(Rozpis::build(['start' => '2025-04-17', 'thingsToTake' => 'jen buzolu'], null, DEFAULTS, 'T')['sections'][6]['value'], 'jen buzolu');
});
test('build uses defaults when empty', function () {
    $r = Rozpis::build(['start' => '2025-04-17', 'end' => '2025-04-21', 'transport' => '  '], null, DEFAULTS, 'TK X 2025');
    eq($r['deadline'], '17. března 2025');
    eq(array_column($r['sections'], 'label'), ['Sraz', 'Doprava', 'Ubytování', 'Jídlo', 'Návrat', 'Program', 'S sebou']);
    eq($r['sections'][0]['value'], 'Kr. Pole nádraží');
    eq($r['sections'][1]['value'], 'auty');
});
test('build uses event and members values', function () {
    $r = Rozpis::build(['start' => '2025-04-17', 'end' => '2025-04-21', 'meetTime' => '8:00', 'meetPlace' => 'hlavní nádraží', 'food' => 'plná penze'],
        ['entries' => ['2025-03-01', '2025-03-20']], DEFAULTS, 'TK X 2025');
    eq($r['deadline'], '1. března 2025');
    eq($r['sections'][0]['value'], '8:00, hlavní nádraží');
    eq($r['sections'][3]['value'], 'plná penze');
});
test('meetTime kept with default place', fn() =>
    eq(Rozpis::build(['start' => '2025-04-17', 'meetTime' => '8:00'], null, DEFAULTS, 'T')['sections'][0]['value'], '8:00, Kr. Pole nádraží'));
test('invalid members deadline falls back to default', function () {
    foreach ([['1970-01-01'], ['2025-03-01 12:00'], ['2025-05-01'], []] as $entries) {
        $r = Rozpis::build(['start' => '2025-04-17'], ['entries' => $entries], DEFAULTS, 'T');
        eq($r['deadline'], '17. března 2025', json_encode($entries));
    }
});
test('html escapes, footer line, logos', function () {
    $r = Rozpis::build(['start' => '2025-04-17', 'end' => '2025-04-19'], null, DEFAULTS, 'TK <A&B> "q"');
    $html = Rozpis::html($r, ['line' => 'LINE <x>', 'logo' => 'logo-brno.png'], fn($m) => "<md>$m</md>");
    ok(strpos($html, 'TK &lt;A&amp;B&gt; &quot;q&quot;') !== false);
    ok(strpos($html, '<A&B>') === false);
    ok(strpos($html, '<md>auty</md>') !== false);
    ok(strpos($html, '<div id="footer">LINE &lt;x&gt;</div>') !== false, 'footer');
    eq(substr_count($html, 'src="data:image/png;base64,'), 2, 'club + brno logo');
    ok(strpos($html, '<table class="days">') !== false && strpos($html, '<li>ČT<ul><li>dopo: cesta</li>') !== false, 'days');
    ok(strpos($html, '<md>- ') === false || strpos($html, 'dopo: cesta</md>') === false, 'program not via markdown');
});
test('logo: jpeg mime, missing file and path traversal ignored', function () {
    ok(str_starts_with((string)Rozpis::logoDataUri('logo-jmk.jpg'), 'data:image/jpeg;base64,'));
    eq(Rozpis::logoDataUri('nope.png'), null);
    eq(Rozpis::logoDataUri(null), null);
    eq(Rozpis::logoDataUri('../Rozpis.php'), null);
    $html = Rozpis::html(Rozpis::build(['start' => '2025-04-17'], null, DEFAULTS, 'T'), ['line' => 'L'], fn($m) => $m);
    eq(substr_count($html, 'src="data:'), 1, 'only club logo');
});
test('days split into two columns, left gets extra day', function () {
    $html = Rozpis::html(Rozpis::build(['start' => '2025-04-17', 'end' => '2025-04-21'], null, DEFAULTS, 'T'), ['line' => 'L'], fn($m) => $m);
    $cols = explode('</td><td>', explode('<table class="days"><tr><td>', $html)[1]);
    eq(substr_count($cols[0], '<li>') - substr_count($cols[0], '<li>dopo') - substr_count($cols[0], '<li>odpo'), 3);
    ok(strpos($cols[1], '<li>NE<ul>') !== false && strpos($cols[1], '<li>PO<ul>') !== false);
});

test('malformed date throws InvalidArgumentException', function () {
    $threw = false;
    try { Format::longDate('nonsense'); } catch (\InvalidArgumentException $e) { $threw = str_contains($e->getMessage(), 'nonsense'); }
    ok($threw, 'expected InvalidArgumentException');
});
