<?php
namespace Grav\Plugin\Dotace;

use DateTimeImmutable;

class Rozpis
{
    private const SHORT_DAYS = ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'];

    private static function filled($v): bool
    {
        return $v !== null && trim((string)$v) !== '';
    }

    private static function pick(array $event, array $defaults, string $key): string
    {
        return self::filled($event[$key] ?? null) ? trim($event[$key]) : trim((string)($defaults[$key] ?? ''));
    }

    /** Members deadline if strict Y-m-d, after 1970-01-01 and not after event start; else null. */
    private static function validDeadline($value, string $start): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        $value = trim($value);
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($d === false || $d->format('Y-m-d') !== $value || $value <= '1970-01-01' || $value > $start) {
            return null;
        }
        return $value;
    }

    public static function program(string $start, ?string $end): string
    {
        $s = DateTimeImmutable::createFromFormat('!Y-m-d', $start);
        $e = self::filled($end) ? DateTimeImmutable::createFromFormat('!Y-m-d', trim($end)) : $s;
        if ($e < $s) {
            $e = $s;
        }
        $count = (int)$s->diff($e)->days + 1;
        $lines = [];
        for ($i = 0; $i < $count; $i++) {
            $label = self::SHORT_DAYS[(int)$s->modify("+$i days")->format('w')];
            if ($count === 1) {
                $text = 'trénink';
            } elseif ($i === 0) {
                $text = 'dopoledne cesta, odpoledne první trénink';
            } elseif ($i === $count - 1) {
                $text = 'dopoledne mapová fáze, odpoledne cesta zpět';
            } else {
                $text = ($i % 2 === 1 ? '2×' : '1×') . ' mapová fáze';
            }
            $lines[] = "$label: $text";
        }
        return implode("  \n", $lines);
    }

    public static function build(array $event, ?array $race, array $defaults, string $title): array
    {
        $start = $event['start'];
        $end = $event['end'] ?? null;

        $place = self::pick($event, $defaults, 'meetPlace');
        $sraz = self::filled($event['meetTime'] ?? null) ? trim($event['meetTime']) . ', ' . $place : $place;
        $program = self::filled($event['program'] ?? null) ? trim($event['program']) : self::program($start, $end);

        $entry = $race['entries'][0] ?? null;
        $deadline = self::validDeadline($entry, $start) ?? Format::defaultDeadline($start);

        return [
            'title' => $title,
            'dates' => Format::dateRange($start, $end),
            'deadline' => Format::longDate($deadline),
            'sections' => [
                ['label' => 'Sraz', 'value' => $sraz],
                ['label' => 'Doprava', 'value' => self::pick($event, $defaults, 'transport')],
                ['label' => 'Ubytování', 'value' => self::pick($event, $defaults, 'accomodation')],
                ['label' => 'Jídlo', 'value' => self::pick($event, $defaults, 'food')],
                ['label' => 'Návrat', 'value' => self::pick($event, $defaults, 'return')],
                ['label' => 'Program', 'value' => $program],
                ['label' => 'S sebou', 'value' => self::pick($event, $defaults, 'thingsToTake')],
            ],
        ];
    }

    public static function html(array $rozpis, string $subsidyLine, callable $markdown): string
    {
        $e = fn(string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
        $rows = '';
        foreach ($rozpis['sections'] as $section) {
            $rows .= '<tr><td class="label">' . $e($section['label']) . '</td><td>' . $markdown($section['value']) . "</td></tr>\n";
        }
        return '<!DOCTYPE html><html><head><meta charset="utf-8">'
            . '<style>body{font-family:"DejaVu Sans",sans-serif;font-size:11pt}'
            . 'table{border-collapse:collapse;width:100%}td{vertical-align:top;padding:4px 8px}'
            . 'td.label{font-weight:bold;width:25%}p{margin:0}</style></head><body>'
            . '<h1>' . $e($rozpis['title']) . '</h1>'
            . '<p>' . $e($rozpis['dates']) . '</p>'
            . '<p>Přihlášky do ' . $e($rozpis['deadline']) . '</p>'
            . "<table>\n" . $rows . "</table>\n"
            . '<p>' . $e($subsidyLine) . '</p>'
            . '</body></html>';
    }
}
