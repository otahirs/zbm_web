<?php
namespace Grav\Plugin\Dotace;

class Seznam
{
    public static function birthYear(?string $reg, int $currentYear): ?int
    {
        if ($reg === null || !preg_match('/^\d{4}\z/', $reg)) return null;
        $yy = (int) substr($reg, 0, 2);
        return $yy > $currentYear % 100 ? 1900 + $yy : 2000 + $yy;
    }

    /** @return list<array{int,string,?int,string}> */
    public static function rows(array $everyone, int $currentYear): array
    {
        $people = [];
        foreach ($everyone as $e) {
            $people[] = [
                trim(($e['surname'] ?? '') . ' ' . ($e['name'] ?? '')),
                self::birthYear($e['reg'] ?? null, $currentYear),
            ];
        }
        $collator = new \Collator('cs_CZ');
        // index tie-break keeps the sort stable for identical names
        $order = array_keys($people);
        usort($order, fn($a, $b) => $collator->compare($people[$a][0], $people[$b][0]) ?: $a <=> $b);
        $rows = [];
        foreach ($order as $i => $idx) {
            $rows[] = [$i + 1, $people[$idx][0], $people[$idx][1], 'ZBM'];
        }
        return $rows;
    }
}
