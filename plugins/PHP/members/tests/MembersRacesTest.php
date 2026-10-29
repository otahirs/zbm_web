<?php
use Grav\Plugin\Members\MembersRaces;

function v3race(array $over = []): array
{
    return $over + [
        'race_id' => 7, 'dates' => ['2026-04-12', '2026-04-13'], 'entries' => ['2026-03-01', '2026-03-20'],
        'name' => 'Jarní závod', 'cancelled' => false, 'club' => 'ZBM', 'link' => 'oris.ceskyorientak.cz/Zavod?id=42',
        'place' => 'Brno', 'type' => 'Závod', 'sport' => null, 'rankings' => ['Celostátní'],
        'rank21' => '0', 'note' => 'pozn.', 'transport' => 3, 'accommodation' => 1, 'categories' => ['D14'],
    ];
}

test('race to event', function () {
    eq(MembersRaces::toEvent(v3race()), [
        'id' => '2026-race_7', 'start' => '2026-04-12', 'end' => '2026-04-13', 'title' => 'Jarní závod',
        'link' => 'https://oris.ceskyorientak.cz/Zavod?id=42', 'orisid' => '42', 'place' => 'Brno', 'type' => 'Z',
        'dorost' => '1', 'hobby' => '1', 'note' => 'pozn.',
        'transport' => 'auty dle přihláškového systému', 'accomodation' => 'společné ubytování',
    ]);
});
test('single day race ends on start, no transport/accommodation, null note', function () {
    $e = MembersRaces::toEvent(v3race(['dates' => ['2026-05-01'], 'transport' => 0, 'accommodation' => 0, 'note' => null, 'link' => null]));
    eq([$e['start'], $e['end'], $e['note'], $e['link']], ['2026-05-01', '2026-05-01', '', '']);
    ok(!isset($e['transport']) && !isset($e['accomodation']) && !isset($e['orisid']));
});
test('transport yes / auto yes is shared transport', function () {
    eq(MembersRaces::toEvent(v3race(['transport' => 1]))['transport'], 'společná doprava');
    eq(MembersRaces::toEvent(v3race(['transport' => 2]))['transport'], 'společná doprava');
    eq(MembersRaces::toEvent(v3race(['accommodation' => 2]))['accomodation'], 'společné ubytování');
});
test('rankings to groups, D+ in name adds dorost', function () {
    $groups = fn(array $r) => array_values(array_intersect(array_keys(MembersRaces::toEvent($r)),
        ['dorost', 'hobby', 'zaci2', 'zaci1', 'pulci2', 'pulci1', 'zabicky']));
    eq($groups(v3race(['rankings' => ['Oblastní']])), ['dorost', 'hobby', 'zaci2', 'zaci1', 'pulci2', 'pulci1', 'zabicky']);
    eq($groups(v3race(['rankings' => ['Morava']])), ['dorost', 'hobby', 'zaci2', 'zaci1']);
    eq($groups(v3race(['rankings' => ['Čechy', 'Veřejný']])), []);
    eq($groups(v3race(['rankings' => [], 'name' => 'D+: TK Španělsko'])), ['dorost']);
});
test('only Z/S/T/V types are imported', function () {
    foreach (['Závod' => 'Z', 'Soustředění' => 'S', 'Trénink' => 'T', 'Sportovní vyšetření' => 'V'] as $name => $code) {
        ok(MembersRaces::isImported(v3race(['type' => $name])));
        eq(MembersRaces::toEvent(v3race(['type' => $name]))['type'], $code);
    }
    ok(!MembersRaces::isImported(v3race(['type' => 'Nákup oblečení'])));
    ok(!MembersRaces::isImported(v3race(['type' => 'Jiné'])));
    ok(!MembersRaces::isImported(v3race(['dates' => []])));
});
test('links: http upgraded, https kept, scheme-less gets https', function () {
    eq(MembersRaces::toEvent(v3race(['link' => 'http://a.cz/x']))['link'], 'https://a.cz/x');
    eq(MembersRaces::toEvent(v3race(['link' => 'https://a.cz']))['link'], 'https://a.cz');
    eq(MembersRaces::toEvent(v3race(['link' => 'a.cz']))['link'], 'https://a.cz');
});
test('event id uses year of first day', fn() =>
    eq(MembersRaces::eventId(v3race(['race_id' => 2510, 'dates' => ['2027-07-23', '2027-07-31']])), '2027-race_2510'));
test('home entries: first deadline, skips cancelled and no-date', function () {
    eq(MembersRaces::entriesByDate([
        v3race(['name' => 'A', 'entries' => ['2026-03-01', '2026-03-20']]),
        v3race(['name' => 'B', 'entries' => ['2026-03-01'], 'dates' => ['2026-05-01']]),
        v3race(['name' => 'C', 'cancelled' => true]),
        v3race(['name' => 'D', 'entries' => ['1970-01-01']]),
        'garbage',
    ]), ['2026-03-01' => [['name' => 'A', 'date' => '2026-04-12'], ['name' => 'B', 'date' => '2026-05-01']]]);
});
