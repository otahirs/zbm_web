<?php
namespace Grav\Plugin;
use Grav\Common\Cache;
use Grav\Common\HTTP\Response;
use Grav\Plugin\Utils;
use Grav\Common\Grav;

require_once(__DIR__ . "/Utils.php");

/**
 * Weekly training plan.
 *
 * Data model:
 *  - /auth/plan2/templates  header.templates  name => week (monday..sunday => list of trainings)
 *                           header.calendar   list of {from: 'Y-m-d' (Monday), template: name}
 *                                             a template applies from its Monday until the next row
 *  - /auth/plan2            header.plan       'Y-m-d' (Monday) => week, only weeks edited by hand
 *                                             (overrides of the calendar template)
 *
 * A week shown anywhere = override for that Monday if present, otherwise the template
 * the calendar assigns to that Monday. Nothing is copied ahead of time, no weekly cron.
 */
class WeeklyPlan extends \Grav\Common\Twig\TwigExtension
{
    public function getName()
    {
        return 'WeeklyPlan';
    }
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('phpSavePlan2', [$this, 'SavePlan2']),
            new \Twig_SimpleFunction('phpLoadPlanFromTemplate', [$this, 'LoadPlanFromTemplate']),
            new \Twig_SimpleFunction('phpSavePlan2Template', [$this, 'SavePlan2Template']),
            new \Twig_SimpleFunction('phpCreatePlanTemplate', [$this, 'CreatePlanTemplate']),
            new \Twig_SimpleFunction('phpDeletePlanTemplate', [$this, 'DeletePlanTemplate']),
            new \Twig_SimpleFunction('phpSuggestPlanCalendar', [$this, 'SuggestCalendar']),
            new \Twig_SimpleFunction('phpPlanWeeks', [$this, 'GetWeeks']),
            new \Twig_SimpleFunction('phpPlanCalendar', [$this, 'GetCalendar']),
        ];
    }

    const PLAN_URL = "/auth/plan2/";
    const PLAN_TEMPLATES_URL = "/auth/plan2/templates";
    const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const LEGACY_WEEKS = ['thisWeek', 'nextWeek', 'next2Week', 'next3Week'];
    const TZ = 'Europe/Prague';

    /* ---------- dates ---------- */

    static function tz() {
        return new \DateTimeZone(self::TZ);
    }

    /** Monday (Y-m-d) of the week containing $date ('Y-m-d', timestamp or relative string). */
    static function mondayOf($date) {
        if (is_int($date)) {
            $d = (new \DateTime('@' . $date))->setTimezone(self::tz());
        } else {
            $d = new \DateTime($date ?: 'now', self::tz());
        }
        $d->setTime(0, 0);
        $d->modify('-' . ((int)$d->format('N') - 1) . ' days');
        return $d->format('Y-m-d');
    }

    static function addWeeks($monday, $n) {
        $d = new \DateTime($monday, self::tz());
        $d->modify(($n >= 0 ? '+' : '') . $n . ' weeks');
        return $d->format('Y-m-d');
    }

    /** YAML may turn unquoted dates into unix timestamps - accept both. */
    static function dateKey($key) {
        if (is_int($key) || (is_string($key) && ctype_digit($key) && strlen($key) > 8)) {
            return self::mondayOf((int)$key);
        }
        if (is_string($key) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $key)) {
            return self::mondayOf($key);
        }
        return null;
    }

    /* ---------- week normalisation ---------- */

    /** Clean one week: drop empty rows, reindex, keep all 7 days. */
    static function cleanWeek($week) {
        $out = [];
        foreach (self::DAYS as $day) {
            $rows = [];
            foreach ((array)($week[$day] ?? []) as $row) {
                if (!is_array($row)) continue;
                $group = array_values(array_filter((array)($row['group'] ?? []), 'strlen'));
                $name = trim((string)($row['name'] ?? ''));
                $time = trim((string)($row['time'] ?? ''));
                $place = trim((string)($row['place'] ?? ''));
                if (!$group && $name === '' && $time === '' && $place === '') continue;
                $rows[] = ['group' => $group, 'name' => $name, 'time' => $time, 'place' => $place];
            }
            $out[$day] = $rows;
        }
        return $out;
    }

    /** Comparable form of a week (order of rows and groups does not matter). */
    static function weekSignature($week) {
        $sig = [];
        foreach (self::cleanWeek($week) as $day => $rows) {
            $list = [];
            foreach ($rows as $r) {
                $g = $r['group'];
                sort($g);
                $list[] = json_encode([$g, $r['name'], $r['time'], $r['place']], JSON_UNESCAPED_UNICODE);
            }
            sort($list);
            $sig[$day] = $list;
        }
        return json_encode($sig, JSON_UNESCAPED_UNICODE);
    }

    /* ---------- storage ---------- */

    static function pages() {
        return Grav::instance()['pages'];
    }

    static function templatesPage() {
        return self::pages()->find(self::PLAN_TEMPLATES_URL);
    }

    static function planPage() {
        return self::pages()->find(self::PLAN_URL);
    }

    static function templatesHeader() {
        return (array)self::templatesPage()->header();
    }

    /** Calendar rows sorted by date, dates normalised to Mondays. */
    static function calendar($tplHeader = null) {
        $tplHeader = $tplHeader ?? self::templatesHeader();
        $rows = [];
        foreach ((array)($tplHeader['calendar'] ?? []) as $row) {
            $row = (array)$row;
            $from = self::dateKey($row['from'] ?? null);
            $template = (string)($row['template'] ?? '');
            if ($from === null || $template === '') continue;
            $rows[$from] = ['from' => $from, 'template' => $template];
        }
        ksort($rows);
        return array_values($rows);
    }

    static function templateNameFor($monday, $calendar) {
        $name = null;
        foreach ($calendar as $row) {
            if ($row['from'] <= $monday) $name = $row['template'];
            else break;
        }
        return $name;
    }

    /**
     * Read plan overrides keyed by Monday. Converts the legacy relative format
     * (thisWeek..next3Week shifted by cron) to dated weeks and saves it once.
     */
    static function overrides() {
        $page = self::planPage();
        $header = (array)$page->header();
        $plan = (array)($header['plan'] ?? []);

        $legacy = array_intersect(array_keys($plan), self::LEGACY_WEEKS);
        $out = [];
        foreach ($plan as $key => $week) {
            $monday = self::dateKey($key);
            if ($monday !== null) $out[$monday] = (array)$week;
        }
        if ($legacy) {
            $tplHeader = self::templatesHeader();
            $calendar = self::calendar($tplHeader);
            $templates = (array)($tplHeader['templates'] ?? []);
            $thisMonday = self::mondayOf('now');
            foreach (self::LEGACY_WEEKS as $i => $key) {
                if (!isset($plan[$key])) continue;
                $monday = self::addWeeks($thisMonday, $i);
                $tplName = self::templateNameFor($monday, $calendar);
                $tpl = $tplName !== null ? ($templates[$tplName] ?? []) : [];
                if (self::weekSignature($plan[$key]) !== self::weekSignature($tpl)) {
                    $out[$monday] = self::cleanWeek($plan[$key]);
                }
            }
            ksort($out);
            $header['plan'] = $out;
            $page->header($header);
            $page->save();
            Utils::log("Plan | migrated to dated weeks");
            Cache::clearCache('cache-only');
        }
        return $out;
    }

    /* ---------- read API (twig) ---------- */

    /**
     * Weeks for display.
     * @param string|null $from  any date inside the first week, default this week
     * @param int $count
     * @return array list of [monday, sunday, days, template, override]
     */
    public static function GetWeeks($from = null, $count = 2) {
        $tplHeader = self::templatesHeader();
        $templates = (array)($tplHeader['templates'] ?? []);
        $calendar = self::calendar($tplHeader);
        $overrides = self::overrides();

        $monday = self::mondayOf($from ?: 'now');
        $weeks = [];
        for ($i = 0; $i < $count; $i++) {
            $tplName = self::templateNameFor($monday, $calendar);
            $override = array_key_exists($monday, $overrides);
            $days = $override ? $overrides[$monday] : ($tplName !== null ? ($templates[$tplName] ?? []) : []);
            $weeks[] = [
                'monday' => $monday,
                'sunday' => (new \DateTime($monday, self::tz()))->modify('+6 days')->format('Y-m-d'),
                'days' => self::cleanWeek($days),
                'template' => $tplName,
                'override' => $override,
            ];
            $monday = self::addWeeks($monday, 1);
        }
        return $weeks;
    }

    public static function GetCalendar() {
        return self::calendar();
    }

    /* ---------- write API (POST endpoints) ---------- */

    static function edit_events_groups($events) {
        foreach($events as $id => $groups) {
            $year = substr($id, 0, 4);
            $page_url = "/data/events/{$year}/{$id}";

            $page = Grav::instance()['page']->find($page_url);
            if (!$page) continue;
            $frontmatter = (array)$page->header();
            $frontmatter["taxonomy"]["skupina"] = $groups;

            $page->header($frontmatter);
            $page->save();
        }
    }

    /**
     * Save edited weeks: POST plan[Y-m-d][day][rowId]{group[],name,time,place}.
     * A week equal to its calendar template is stored as "no override".
     */
    public static function SavePlan2(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["plan"]) && empty($_POST["events"])) return;

        $tplHeader = self::templatesHeader();
        $templates = (array)($tplHeader['templates'] ?? []);
        $calendar = self::calendar($tplHeader);
        $overrides = self::overrides();

        foreach ((array)($_POST["plan"] ?? []) as $key => $week) {
            $monday = self::dateKey($key);
            if ($monday === null) continue;
            $week = self::cleanWeek($week);
            $tplName = self::templateNameFor($monday, $calendar);
            $tpl = $tplName !== null ? ($templates[$tplName] ?? []) : [];
            if (self::weekSignature($week) === self::weekSignature($tpl)) {
                unset($overrides[$monday]);
            } else {
                $overrides[$monday] = $week;
            }
        }

        // forget overrides of weeks that are long gone
        $keepFrom = self::addWeeks(self::mondayOf('now'), -4);
        foreach (array_keys($overrides) as $monday) {
            if ($monday < $keepFrom) unset($overrides[$monday]);
        }
        ksort($overrides);

        if (!empty($_POST["events"])) {
            self::edit_events_groups($_POST["events"]);
        }

        $page = self::planPage();
        $frontmatter = (array)$page->header();
        $frontmatter["plan"] = $overrides;
        $page->header($frontmatter);
        $page->save();

        Utils::log("Plan | edited");
        Cache::clearCache('cache-only');
    }

    /**
     * POST week=Y-m-d, template=name  -> week becomes a copy of that template (override)
     * POST week=Y-m-d, template=''    -> drop override, week follows the calendar again
     */
    public static function LoadPlanFromTemplate(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        $monday = self::dateKey($_POST["week"] ?? null);
        if ($monday === null) return;
        $template = (string)($_POST["template"] ?? '');

        $templates = (array)(self::templatesHeader()['templates'] ?? []);
        $overrides = self::overrides();
        if ($template === '') {
            unset($overrides[$monday]);
        } elseif (isset($templates[$template])) {
            $overrides[$monday] = self::cleanWeek($templates[$template]);
        } else {
            return;
        }
        ksort($overrides);

        $page = self::planPage();
        $frontmatter = (array)$page->header();
        $frontmatter["plan"] = $overrides;
        $page->header($frontmatter);
        $page->save();

        Utils::log("Plan | loaded from template | {$monday} => " . ($template === '' ? '(kalendář)' : $template));
        Cache::clearCache('cache-only');
    }

    /** POST template[name][day][rowId]{...}, calendar_present=1, calendar[i]{from,template} */
    static function SavePlan2Template(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["template"]) && empty($_POST["calendar_present"])) return;

        $page = self::templatesPage();
        $frontmatter = (array)$page->header();
        $templates = (array)($frontmatter["templates"] ?? []);

        foreach ((array)($_POST["template"] ?? []) as $name => $week) {
            if (!array_key_exists($name, $templates)) continue;
            $templates[$name] = self::cleanWeek($week);
        }
        $frontmatter["templates"] = $templates;

        if (!empty($_POST["calendar_present"])) {
            $rows = [];
            foreach ((array)($_POST["calendar"] ?? []) as $row) {
                $from = self::dateKey($row['from'] ?? null);
                $template = (string)($row['template'] ?? '');
                if ($from === null || !array_key_exists($template, $templates)) continue;
                $rows[$from] = ['from' => $from, 'template' => $template];
            }
            ksort($rows);
            $frontmatter["calendar"] = array_values($rows);
        }
        unset($frontmatter["defaultTemplate"]);

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplates | edited ");
        Cache::clearCache('cache-only');
    }

    public static function DeletePlanTemplate() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["deletedTemplate"])) return;
        $del_template = $_POST["deletedTemplate"];

        $page = self::templatesPage();
        $frontmatter = (array)$page->header();
        foreach (self::calendar($frontmatter) as $row) {
            if ($row['template'] === $del_template) {
                Utils::return_ERROR("Šablona je použitá v kalendáři ({$row['from']}), nejdřív ji z kalendáře odeberte.");
            }
        }
        unset($frontmatter["templates"][$del_template]);

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplate | deleted | {$del_template}");
        Cache::clearCache('cache-only');
    }

    public static function CreatePlanTemplate() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["templateName"])) return;
        $template_name = Utils::trim_all($_POST["templateName"]);
        $page = self::templatesPage();
        $frontmatter = (array)$page->header();
        $frontmatter["templates"] = (array)($frontmatter["templates"] ?? []);

        $counter = 0;
        $new_template_name = $template_name;
        while (array_key_exists($new_template_name, $frontmatter["templates"])) {
            $counter++;
            $new_template_name = $template_name . "(". $counter .")";
        }

        $frontmatter["templates"][$new_template_name] = self::cleanWeek([]);

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplate | created | {$template_name}");
        Cache::clearCache('cache-only');
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($new_template_name);
    }

    /* ---------- calendar proposal for a season ---------- */

    static function easterSunday($y) {
        // anonymous Gregorian algorithm (no ext-calendar needed)
        $a = $y % 19; $b = intdiv($y, 100); $c = $y % 100;
        $d = intdiv($b, 4); $e = $b % 4; $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3); $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4); $k = $c % 4; $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        return sprintf('%04d-%02d-%02d', $y, $month, $day);
    }

    /** Saturday of "MČR štafet / klubů" weekend from ORIS, null if not found. */
    static function mcrClubsDate($y) {
        try {
            $body = Response::get("https://oris.ceskyorientak.cz/API/?format=json&method=getEventList&sport=1&datefrom={$y}-09-01&dateto={$y}-10-31");
            $data = json_decode($body, true)['Data'] ?? [];
        } catch (\Throwable $e) {
            return null;
        }
        $found = null;
        foreach ((array)$data as $event) {
            $name = mb_strtolower($event['Name'] ?? '');
            if (strpos($name, 'mistrovství') === false && strpos($name, 'mčr') === false) continue;
            if (strpos($name, 'štafet') === false && strpos($name, 'klubů') === false) continue;
            $date = $event['Date'] ?? null;
            if ($date && ($found === null || $date < $found)) $found = $date;
        }
        return $found;
    }

    /**
     * Calendar rows for one season (year Y), using the club's rules:
     *   Závodní   Monday after Easter
     *   Prázdniny week of the first weekday of July
     *   Závodní   week containing 1 Sep
     *   Volno     Monday after MČR štafet/klubů (ORIS; fallback 2nd weekend of October)
     *   Zimní     week containing 1 Nov
     * POST year=Y -> JSON {rows: [...], notes: [...]}
     */
    public static function SuggestCalendar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        $y = (int)($_POST["year"] ?? 0);
        if ($y < 2000 || $y > 2100) Utils::return_ERROR("Neplatný rok");
        $notes = [];

        $easter = new \DateTime(self::easterSunday($y), self::tz());
        $afterEaster = (clone $easter)->modify('+1 day')->format('Y-m-d');

        $july = new \DateTime("{$y}-07-01", self::tz());
        while ((int)$july->format('N') >= 6) $july->modify('+1 day');

        $mcr = self::mcrClubsDate($y);
        if ($mcr === null) {
            $oct = new \DateTime("{$y}-10-01", self::tz());
            while ((int)$oct->format('N') !== 6) $oct->modify('+1 day');
            $mcr = $oct->modify('+7 days')->format('Y-m-d');
            $notes[] = "MČR štafet/klubů v ORIS nenalezeno, odhad: {$mcr}";
        } else {
            $notes[] = "MČR štafet/klubů podle ORIS: {$mcr}";
        }

        $rows = [
            ['from' => self::mondayOf($afterEaster), 'template' => 'Závodní'],
            ['from' => self::mondayOf($july->format('Y-m-d')), 'template' => 'Prázdniny'],
            ['from' => self::mondayOf("{$y}-09-01"), 'template' => 'Závodní'],
            ['from' => self::addWeeks(self::mondayOf($mcr), 1), 'template' => 'Volno'],
            ['from' => self::mondayOf("{$y}-11-01"), 'template' => 'Zimní'],
        ];

        header('Content-type:application/json;charset=utf-8');
        echo json_encode(['rows' => $rows, 'notes' => $notes], JSON_UNESCAPED_UNICODE);
    }
}
?>
