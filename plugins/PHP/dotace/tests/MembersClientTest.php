<?php
use Grav\Plugin\Dotace\{MembersClient, MembersAuth};

test('race id from event id', function () {
    eq(MembersClient::raceIdFromEventId('2025-race_2148'), 2148);
    eq(MembersClient::raceIdFromEventId('2025-tk-podzim'), null);
    eq(MembersClient::raceIdFromEventId("2025-race_2148\n"), null);
});
function fakeAuth(array &$posts, array $responses, ?string $file = null, int $now = 1000000): MembersAuth
{
    $file = $file ?? tempnam(sys_get_temp_dir(), 'mt') . '.json';
    $post = function ($url, $body, $h) use (&$posts, &$responses) {
        $posts[] = [$url, json_decode($body, true), $h];
        return array_shift($responses) ?? ['status' => 500, 'body' => ''];
    };
    return new MembersAuth($post, 'web-zbm', 'S3cret', $file, 'https://members.eob.cz/api/3/zbm', fn() => $now);
}
function loginOk(string $token, int $exp): array
{
    return ['status' => 200, 'body' => json_encode(['access_token' => $token, 'expiration' => $exp, 'device' => 'd', 'user_id' => 1])];
}

test('no auth sends no bearer', function () {
    $seen = [];
    $get = function ($url, $h) use (&$seen) { $seen[] = [$url, $h]; return ['status' => 200, 'body' => '{"entries":[],"everyone":[]}']; };
    (new MembersClient($get))->race(7);
    eq($seen[0][0], 'https://members.eob.cz/api/3/zbm/race/7');
    eq($seen[0][1], ['Accept: application/json']);
});
test('auth logs in once and caches the token in a 0600 file', function () {
    $posts = [];
    $file = sys_get_temp_dir() . '/mtok-' . bin2hex(random_bytes(4)) . '/token.json';
    $auth = fakeAuth($posts, [loginOk('T1', 1000000 + 90 * 86400)], $file);
    eq($auth->token(), 'T1');
    eq($auth->token(), 'T1');
    eq(count($posts), 1, 'one login');
    eq($posts[0][0], 'https://members.eob.cz/api/3/zbm/system/login');
    eq([$posts[0][1]['username'], $posts[0][1]['password']], ['web-zbm', 'S3cret']);
    eq(fileperms($file) & 0777, 0600);
    $posts2 = [];
    eq(fakeAuth($posts2, [], $file)->token(), 'T1', 'second instance reads cache');
    eq(count($posts2), 0);
    unlink($file); rmdir(dirname($file));
});
test('auth renews 7 days before expiry and on demand', function () {
    $posts = [];
    $file = tempnam(sys_get_temp_dir(), 'mt');
    file_put_contents($file, json_encode(['access_token' => 'OLD', 'expiration' => 1000000 + 6 * 86400]));
    eq(fakeAuth($posts, [loginOk('NEW', 1000000 + 90 * 86400)], $file)->token(), 'NEW');
    file_put_contents($file, json_encode(['access_token' => 'OLD', 'expiration' => 1000000 + 30 * 86400]));
    $posts = [];
    eq(fakeAuth($posts, [loginOk('NEW2', 1000000 + 90 * 86400)], $file)->token(true), 'NEW2');
    eq(count($posts), 1);
    unlink($file);
});
test('auth failures throw without the password', function () {
    foreach ([['status' => 403, 'body' => '{"message":"Wrong password."}'], ['status' => 200, 'body' => '{}'], ['status' => 0, 'body' => '']] as $res) {
        $posts = [];
        $msg = null;
        try { fakeAuth($posts, [$res])->token(); } catch (\RuntimeException $e) { $msg = $e->getMessage(); }
        ok($msg !== null && str_starts_with($msg, 'members: '), json_encode($res));
        ok(!str_contains($msg, 'S3cret'));
    }
});
test('client sends bearer and retries once on 401 with a renewed token', function () {
    $posts = [];
    $file = tempnam(sys_get_temp_dir(), 'mt');
    file_put_contents($file, json_encode(['access_token' => 'STALE', 'expiration' => 1000000 + 60 * 86400]));
    $auth = fakeAuth($posts, [loginOk('FRESH', 1000000 + 90 * 86400)], $file);
    $seen = [];
    $get = function ($url, $h) use (&$seen) {
        $seen[] = $h;
        return in_array('Authorization: Bearer FRESH', $h, true)
            ? ['status' => 200, 'body' => '{"entries":[],"everyone":[]}'] : ['status' => 401, 'body' => ''];
    };
    eq((new MembersClient($get, $auth))->race(7)['everyone'], []);
    eq(count($seen), 2);
    ok(in_array('Authorization: Bearer STALE', $seen[0], true));
    eq(count($posts), 1);
    unlink($file);
});
test('client gives up after the retry also gets 401', function () {
    $posts = [];
    $auth = fakeAuth($posts, [loginOk('A', 1000000 + 90 * 86400), loginOk('B', 1000000 + 90 * 86400)]);
    $calls = 0;
    $msg = null;
    try { (new MembersClient(function () use (&$calls) { $calls++; return ['status' => 401, 'body' => '']; }, $auth))->race(7); }
    catch (\RuntimeException $e) { $msg = $e->getMessage(); }
    eq([$msg, $calls, count($posts)], ['members: HTTP 401', 2, 2]);
});
test('errors throw', function () {
    foreach ([[401, ''], [404, '{}'], [200, 'not json'], [200, '{"entries":[]}'], [0, '']] as [$s, $b]) {
        $thrown = false;
        try { (new MembersClient(fn() => ['status' => $s, 'body' => $b]))->race(1); } catch (\RuntimeException $e) { $thrown = true; }
        ok($thrown, "status $s body $b");
    }
});
