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
test('program 5 days', fn() => eq(Rozpis::program('2025-04-17', '2025-04-21'), implode("  \n", [
    'Čt: dopoledne cesta, odpoledne první trénink',
    'Pá: 2× mapová fáze', 'So: 1× mapová fáze', 'Ne: 2× mapová fáze',
    'Po: dopoledne mapová fáze, odpoledne cesta zpět'])));
test('program 1 and 2 days', function () {
    eq(Rozpis::program('2025-04-05', null), 'So: trénink');
    eq(Rozpis::program('2025-04-05', '2025-04-06'), "So: dopoledne cesta, odpoledne první trénink  \nNe: dopoledne mapová fáze, odpoledne cesta zpět");
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
test('html escapes and ends with subsidy line', function () {
    $html = Rozpis::html(Rozpis::build(['start' => '2025-04-17'], null, DEFAULTS, 'TK <A&B> "q"'), 'LINE', fn($m) => "<md>$m</md>");
    ok(strpos($html, 'TK &lt;A&amp;B&gt; &quot;q&quot;') !== false);
    ok(strpos($html, '<A&B>') === false);
    ok(strpos($html, '<md>auty</md>') !== false);
    ok(strrpos($html, 'LINE') > strrpos($html, 'S sebou'));
});
