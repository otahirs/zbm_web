<?php
require __DIR__ . '/../bootstrap.php';

$GLOBALS['tests'] = [];

function test(string $name, callable $fn): void { $GLOBALS['tests'][] = [$name, $fn]; }
function eq($actual, $expected, string $msg = ''): void {
    if ($actual !== $expected) {
        throw new Exception(trim($msg . ' expected ' . var_export($expected, true) . ', got ' . var_export($actual, true)));
    }
}
function ok(bool $cond, string $msg = ''): void {
    if (!$cond) throw new Exception($msg ?: 'assertion failed');
}

foreach (glob(__DIR__ . '/*Test.php') as $file) require $file;

$failed = 0;
foreach ($GLOBALS['tests'] as [$name, $fn]) {
    try { $fn(); echo "PASS $name\n"; }
    catch (Throwable $e) { $failed++; echo "FAIL $name: " . $e->getMessage() . "\n"; }
}
echo (count($GLOBALS['tests']) - $failed) . '/' . count($GLOBALS['tests']) . " passed\n";
exit($failed ? 1 : 0);
