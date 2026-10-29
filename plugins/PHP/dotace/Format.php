<?php
namespace Grav\Plugin\Dotace;

use DateTimeImmutable;

class Format
{
    private const MONTHS = ['ledna', 'února', 'března', 'dubna', 'května', 'června', 'července', 'srpna', 'září', 'října', 'listopadu', 'prosince'];
    private const DAYS = ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'];

    private static function parse(string $ymd): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('!Y-m-d', $ymd);
    }

    public static function longDate(string $ymd): string
    {
        $d = self::parse($ymd);
        return $d->format('j') . '. ' . self::MONTHS[(int)$d->format('n') - 1] . ' ' . $d->format('Y');
    }

    public static function dateRange(string $start, ?string $end): string
    {
        $s = self::parse($start);
        $e = ($end !== null && trim($end) !== '') ? self::parse(trim($end)) : null;
        $day = fn(DateTimeImmutable $d) => self::DAYS[(int)$d->format('w')] . ' ' . $d->format('j') . '.';
        $month = fn(DateTimeImmutable $d) => self::MONTHS[(int)$d->format('n') - 1];

        if ($e === null || $e <= $s) {
            return $day($s) . ' ' . $month($s) . ' ' . $s->format('Y');
        }
        if ($s->format('Y') !== $e->format('Y')) {
            return $day($s) . ' ' . $month($s) . ' ' . $s->format('Y') . ' — ' . $day($e) . ' ' . $month($e) . ' ' . $e->format('Y');
        }
        if ($s->format('n') !== $e->format('n')) {
            return $day($s) . ' ' . $month($s) . ' — ' . $day($e) . ' ' . $month($e) . ' ' . $e->format('Y');
        }
        return $day($s) . ' — ' . $day($e) . ' ' . $month($e) . ' ' . $e->format('Y');
    }

    /** Start minus one month, clamped to the last day of that month (Y-m-d). */
    public static function defaultDeadline(string $start): string
    {
        $first = self::parse($start)->modify('first day of this month')->modify('-1 month');
        $target = self::parse($start);
        $dayOfMonth = min((int)$target->format('j'), (int)$first->format('t'));
        return $first->setDate((int)$first->format('Y'), (int)$first->format('n'), $dayOfMonth)->format('Y-m-d');
    }
}
