<?php
use Grav\Plugin\Dotace\MembersClient;

test('race id from event id', function () {
    eq(MembersClient::raceIdFromEventId('2025-race_2148'), 2148);
    eq(MembersClient::raceIdFromEventId('2025-tk-podzim'), null);
    eq(MembersClient::raceIdFromEventId("2025-race_2148\n"), null);
});
test('sends bearer only with token', function () {
    $seen = [];
    $get = function ($url, $h) use (&$seen) { $seen[] = [$url, $h]; return ['status' => 200, 'body' => '{"entries":[],"everyone":[]}']; };
    (new MembersClient($get))->race(7);
    (new MembersClient($get, 'TKN'))->race(7);
    eq($seen[0][0], 'https://members.eob.cz/api/3/zbm/race/7');
    ok(!in_array('Authorization: Bearer TKN', $seen[0][1]));
    ok(in_array('Authorization: Bearer TKN', $seen[1][1]));
});
test('errors throw', function () {
    foreach ([[401, ''], [404, '{}'], [200, 'not json'], [200, '{"entries":[]}'], [0, '']] as [$s, $b]) {
        $thrown = false;
        try { (new MembersClient(fn() => ['status' => $s, 'body' => $b]))->race(1); } catch (\RuntimeException $e) { $thrown = true; }
        ok($thrown, "status $s body $b");
    }
});
