<?php
use Grav\Plugin\Dotace\{MembersClient, Package};

function pkgVariants(): array {
    return [
        'brno' => ['label' => 'Brno', 'line' => 'Soustředění bude podpořeno z dotace Magistrátu města Brna – vrcholový sport.'],
        'kraj' => ['label' => 'Kraj', 'line' => 'Soustředění bude podpořeno z dotace Jihomoravského kraje - Podpora sportu'],
    ];
}

function pkgDefaults(): array {
    return [
        'meetPlace' => 'Kr. Pole nádraží', 'transport' => 'auty', 'accomodation' => 'na postelích',
        'food' => 'polopenze', 'return' => 'poslední den akce odpoledne',
        'thingsToTake' => 'sportovní oblečení, boty, buzolu, čip, hodinky',
    ];
}

function pkgCamp(string $id): array {
    return ['id' => $id, 'title' => 'Soustředění Testov', 'event' => ['start' => '2025-04-17', 'end' => '2025-04-21']];
}

/** @return array{0:list<string>,1:array,2:string} names, notes, zip path (caller unlinks) */
function pkgRun(array $camps, array $docs, int $status, ?int &$calls = null): array {
    $calls = 0;
    $body = json_encode(['entries' => [], 'everyone' => [
        ['surname' => 'Novotný', 'name' => 'Adam', 'reg' => '1005'],
        ['surname' => 'Bílá', 'name' => 'Eva', 'reg' => '1101'],
    ]]);
    $members = new MembersClient(function () use (&$calls, $status, $body) {
        $calls++;
        return ['status' => $status, 'body' => $status === 200 ? $body : ''];
    });
    $path = tempnam(sys_get_temp_dir(), 'pkgtest');
    $notes = Package::build($camps, $docs, pkgVariants(), pkgDefaults(), $members, fn($s) => $s, fn($h) => "PDF:$h", $path, 2025);
    return [$notes, $path];
}

function pkgNames(string $path): array {
    $zip = new ZipArchive();
    $zip->open($path);
    $names = [];
    for ($i = 0; $i < $zip->numFiles; $i++) $names[] = $zip->getNameIndex($i);
    $zip->close();
    return $names;
}

function pkgRead(string $path, string $name): string {
    $zip = new ZipArchive();
    $zip->open($path);
    $data = (string) $zip->getFromName($name);
    $zip->close();
    return $data;
}

test('builds all files', function () {
    [$notes, $path] = pkgRun([pkgCamp('2025-race_7')], ['seznam', 'kraj', 'brno'], 200, $calls);
    eq(pkgNames($path), ['2025-race_7_rozpis_brno.pdf', '2025-race_7_rozpis_kraj.pdf', '2025-race_7_seznam_ucastniku.ods']);
    ok(strpos(pkgRead($path, '2025-race_7_rozpis_kraj.pdf'), 'Jihomoravského kraje') !== false);
    ok(strlen(pkgRead($path, '2025-race_7_seznam_ucastniku.ods')) > 0);
    eq($notes, []);
    eq($calls, 1);
    unlink($path);
});
test('members failure keeps rozpis, notes seznam', function () {
    [$notes, $path] = pkgRun([pkgCamp('2025-race_7')], ['brno', 'seznam'], 500);
    eq(pkgNames($path), ['2025-race_7_rozpis_brno.pdf', 'README.txt']);
    ok(strpos(pkgRead($path, 'README.txt'), 'seznam nevygenerován') !== false);
    ok(strpos(pkgRead($path, '2025-race_7_rozpis_brno.pdf'), 'Přihlášky do 17. března 2025') !== false);
    eq(count($notes), 1);
    unlink($path);
});
test('members failure with only rozpis adds no note', function () {
    [$notes, $path] = pkgRun([pkgCamp('2025-race_7')], ['kraj'], 500);
    eq(pkgNames($path), ['2025-race_7_rozpis_kraj.pdf']);
    eq($notes, []);
    unlink($path);
});
test('web-only camp has no seznam', function () {
    [$notes, $path] = pkgRun([pkgCamp('2025-tk-x')], ['seznam'], 200, $calls);
    eq(pkgNames($path), ['README.txt']);
    eq($notes, ['Soustředění Testov: akce není z members, seznam nevygenerován']);
    eq($calls, 0);
    unlink($path);
});
test('empty selection throws, no zip', function () {
    $path = tempnam(sys_get_temp_dir(), 'pkgtest');
    $members = new MembersClient(fn() => ['status' => 200, 'body' => '{}']);
    foreach ([[[], ['brno']], [[pkgCamp('2025-race_7')], []], [[pkgCamp('2025-race_7')], ['bogus']]] as [$camps, $docs]) {
        $thrown = false;
        try {
            Package::build($camps, $docs, pkgVariants(), pkgDefaults(), $members, fn($s) => $s, fn($h) => $h, $path, 2025);
        } catch (\InvalidArgumentException $e) { $thrown = true; ok($e instanceof \Grav\Plugin\Dotace\SelectionException); }
        ok($thrown);
        clearstatcache();
        eq(filesize($path), 0);
    }
    unlink($path);
});
