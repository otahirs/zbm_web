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

    /** @return list<array{day:string,items:list<string>}> one entry per day, two-letter uppercase Czech weekday */
    public static function programDays(string $start, ?string $end): array
    {
        $s = DateTimeImmutable::createFromFormat('!Y-m-d', $start);
        $e = self::filled($end) ? DateTimeImmutable::createFromFormat('!Y-m-d', trim($end)) : $s;
        if ($e === false || $e < $s) {
            $e = $s;
        }
        $count = (int)$s->diff($e)->days + 1;
        $days = [];
        for ($i = 0; $i < $count; $i++) {
            $label = mb_strtoupper(self::SHORT_DAYS[(int)$s->modify("+$i days")->format('w')], 'UTF-8');
            if ($count === 1) {
                $items = ['trénink'];
            } elseif ($i === 0) {
                $items = ['dopo: cesta', 'odpo: první trénink'];
            } elseif ($i === $count - 1) {
                $items = ['dopo: mapová fáze', 'odpo: cesta zpět'];
            } elseif ($i % 2 === 1) {
                $items = ['dopo: mapová fáze', 'odpo: mapová fáze'];
            } else {
                $items = ['dopo: mapová fáze'];
            }
            $days[] = ['day' => $label, 'items' => $items];
        }
        return $days;
    }

    /** Generated programme as a nested markdown list. */
    public static function program(string $start, ?string $end): string
    {
        $lines = [];
        foreach (self::programDays($start, $end) as $d) {
            $lines[] = '- ' . $d['day'];
            foreach ($d['items'] as $item) {
                $lines[] = '    - ' . $item;
            }
        }
        return implode("\n", $lines);
    }

    /** "a, b, c" -> markdown bullet list */
    private static function bulletList(string $csv): string
    {
        $items = array_filter(array_map('trim', explode(',', $csv)), fn($i) => $i !== '');
        return implode("\n", array_map(fn($i) => '- ' . $i, $items));
    }

    public static function build(array $event, ?array $race, array $defaults, string $title): array
    {
        $start = $event['start'];
        $end = $event['end'] ?? null;

        $place = self::pick($event, $defaults, 'meetPlace');
        $sraz = self::filled($event['meetTime'] ?? null) ? trim($event['meetTime']) . ', ' . $place : $place;
        $programSection = self::filled($event['program'] ?? null)
            ? ['label' => 'Program', 'value' => trim($event['program'])]
            : ['label' => 'Program', 'value' => self::program($start, $end), 'days' => self::programDays($start, $end)];
        $things = self::filled($event['thingsToTake'] ?? null)
            ? trim($event['thingsToTake'])
            : self::bulletList((string)($defaults['thingsToTake'] ?? ''));

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
                $programSection,
                ['label' => 'S sebou', 'value' => $things],
            ],
        ];
    }

    private const CLUB_LOGO = 'logo-zbm.png';

    /** Image file from assets/ as a data URI, or null if missing. Only the base name is used. */
    public static function logoDataUri(?string $file): ?string
    {
        if ($file === null || trim($file) === '') {
            return null;
        }
        $path = __DIR__ . '/assets/' . basename($file);
        if (!is_file($path)) {
            return null;
        }
        $mime = str_ends_with(strtolower($path), '.png') ? 'image/png' : 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode((string)file_get_contents($path));
    }

    /**
     * Layout follows the club's Word templates: logos, centred title, bold labels with indented values,
     * two-column generated programme, subsidy line at the page bottom.
     *
     * @param array{line:string,logo?:string} $variant
     */
    public static function html(array $rozpis, array $variant, callable $markdown): string
    {
        $e = fn(string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
        $img = fn(?string $uri, string $class) => $uri === null ? '' : '<img class="' . $class . '" src="' . $uri . '">';

        $sections = '';
        foreach ($rozpis['sections'] as $section) {
            $value = isset($section['days']) ? self::daysHtml($section['days'], $e) : $markdown($section['value']);
            $sections .= '<div class="section"><div class="label">' . $e($section['label']) . '</div>'
                . '<div class="value">' . $value . "</div></div>\n";
        }

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>'
            . '@page{margin:1.5cm 2cm 2.5cm 2cm}'
            . 'body{font-family:"DejaVu Sans",sans-serif;font-size:10.5pt}'
            . 'table.head{width:100%;border-collapse:collapse}table.head td{vertical-align:middle;padding:0}'
            . 'img.club{height:1.3cm}img.partner{height:1.15cm}'
            . 'h1{text-align:center;font-size:19pt;margin:0.5cm 0 0.6cm 0}'
            . 'p{margin:0}.meta{margin-bottom:0.45cm}'
            . '.section{margin-top:0.3cm;page-break-inside:avoid}.label{font-weight:bold}.value{margin-left:1cm}'
            . 'ul{margin:0;padding-left:0.6cm}ul ul{list-style-type:circle}'
            . 'table.days{width:100%;border-collapse:collapse}table.days td{width:50%;vertical-align:top;padding:0}'
            . '#footer{position:fixed;bottom:-1.3cm;left:0;right:0;text-align:center}'
            . '</style></head><body>'
            . '<div id="footer">' . $e($variant['line']) . '</div>'
            . '<table class="head"><tr><td>' . $img(self::logoDataUri(self::CLUB_LOGO), 'club') . '</td>'
            . '<td style="text-align:right">' . $img(self::logoDataUri($variant['logo'] ?? null), 'partner') . '</td></tr></table>'
            . '<h1>' . $e($rozpis['title']) . '</h1>'
            . '<p class="meta">' . $e($rozpis['dates']) . '</p>'
            . '<p class="meta">Přihlášky do ' . $e($rozpis['deadline']) . '</p>'
            . $sections
            . '</body></html>';
    }

    /** Generated programme: day bullets with sub-bullets, split into two columns (left gets the extra day). */
    private static function daysHtml(array $days, callable $e): string
    {
        $cols = array_chunk($days, max(1, (int)ceil(count($days) / 2)));
        $html = '<table class="days"><tr>';
        foreach ([0, 1] as $c) {
            $html .= '<td>';
            if (!empty($cols[$c])) {
                $html .= '<ul>';
                foreach ($cols[$c] as $d) {
                    $html .= '<li>' . $e($d['day']) . '<ul>';
                    foreach ($d['items'] as $item) {
                        $html .= '<li>' . $e($item) . '</li>';
                    }
                    $html .= '</ul></li>';
                }
                $html .= '</ul>';
            }
            $html .= '</td>';
        }
        return $html . '</tr></table>';
    }
}
