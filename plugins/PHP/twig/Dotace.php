<?php
namespace Grav\Plugin;
use Grav\Common\Grav;
use Grav\Plugin\Utils;
use Grav\Plugin\Dotace\MembersClient;
use Grav\Plugin\Dotace\Package;
use Grav\Plugin\Dotace\PdfRenderer;

require_once(__DIR__ . "/Utils.php");

class Dotace extends \Grav\Common\Twig\TwigExtension
{
    const EVENTS_URL = "/data/events";
    const SETTINGS_URL = "/auth/dotace";

    public function getName()
    {
        return 'Dotace';
    }
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('phpDotaceCamps', [$this, 'camps']),
            new \Twig_SimpleFunction('phpDotaceGenerate', [$this, 'generate']),
        ];
    }

    private static function validDate($value): bool
    {
        if (!is_string($value)) return false;
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        return $d !== false && $d->format('Y-m-d') === $value;
    }

    /**
     * Camps (template soustredeni) overlapping the period, sorted by start.
     * @return list<array{id:string,title:string,start:string,end:string,defaultTitle:string,fromMembers:bool,event:array}>
     */
    public static function camps($from, $to): array
    {
        require_once(__DIR__ . "/../dotace/bootstrap.php");
        $from = (string)$from;
        $to = (string)$to;
        $page = Grav::instance()['page'];
        $collection = $page->evaluate(['@page.descendants' => self::EVENTS_URL])->routable();

        $result = [];
        foreach ($collection as $event) {
            $header = json_decode(json_encode($event->header()), true);
            if (!is_array($header)) continue;
            if (($header['template'] ?? null) !== 'soustredeni' && $event->template() !== 'soustredeni') continue;

            $id = (string)($header['id'] ?? '');
            $start = (string)($header['start'] ?? '');
            if ($id === '' || $start === '') continue;
            $end = (string)($header['end'] ?? '');
            if ($end === '') $end = $start;
            if (!($start <= $to && $end >= $from)) continue;

            $title = (string)($header['title'] ?? '');
            $place = (string)($header['place'] ?? '');
            if (trim($place, " \t\n\r\0\x0B?") === '') $place = '';
            $defaultTitle = trim(preg_replace('/\s+/', ' ', $title . ' ' . $place . ' ' . substr($start, 0, 4)));

            $result[] = [
                'id' => $id,
                'title' => $title,
                'start' => $start,
                'end' => (string)($header['end'] ?? ''),
                'defaultTitle' => $defaultTitle,
                'fromMembers' => MembersClient::raceIdFromEventId($id) !== null,
                'event' => $header,
            ];
        }
        usort($result, fn($a, $b) => strcmp($a['start'], $b['start']));
        return $result;
    }

    public static function generate()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        require_once(__DIR__ . "/../dotace/bootstrap.php");
        @set_time_limit(120);

        $grav = Grav::instance();
        if (!$grav['user']->authorize('site.dotace')) {
            http_response_code(403);
            die();
        }

        $from = $_POST['from'] ?? '';
        $to = $_POST['to'] ?? '';
        if (!self::validDate($from) || !self::validDate($to)) {
            Utils::return_ERROR('Neplatné období.');
        }

        $settings = $grav['page']->find(self::SETTINGS_URL);
        $header = $settings ? json_decode(json_encode($settings->header()), true) : [];
        $variants = $header['dotace_variants'] ?? [];
        $defaults = $header['dotace_defaults'] ?? [];

        $selectedIds = is_array($_POST['camps'] ?? null) ? $_POST['camps'] : [];
        $titles = is_array($_POST['titles'] ?? null) ? $_POST['titles'] : [];
        $docs = is_array($_POST['docs'] ?? null) ? array_values(array_filter($_POST['docs'], 'is_string')) : [];

        $camps = [];
        foreach (self::camps($from, $to) as $camp) {
            if (!in_array($camp['id'], $selectedIds, true)) continue;
            $title = isset($titles[$camp['id']]) && is_string($titles[$camp['id']]) ? trim($titles[$camp['id']]) : '';
            $camps[] = [
                'id' => $camp['id'],
                'event' => $camp['event'],
                'title' => $title !== '' ? $title : $camp['defaultTitle'],
            ];
        }

        $token = $grav['config']->get('plugins.PHP.dotace.members_token');
        $members = new MembersClient(MembersClient::gravHttpGet(), $token ? (string)$token : null);
        $markdown = fn($md) => (new \Parsedown())->text($md);
        $pdf = fn($html) => PdfRenderer::render($html);

        $zip = tempnam(sys_get_temp_dir(), 'dotace');
        register_shutdown_function(function () use ($zip) { @unlink($zip); });

        try {
            Package::build($camps, $docs, $variants, $defaults, $members, $markdown, $pdf, $zip, (int)date('Y'));

            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="podklady_dotace_' . $from . '_' . $to . '.zip"');
            header('Content-Length: ' . filesize($zip));
            header('Cache-Control: no-store, private');
            readfile($zip);
            @unlink($zip);
            Utils::log("Dotace | generated | " . count($camps) . " camps");
            exit;
        } catch (\Grav\Plugin\Dotace\SelectionException $e) {
            @unlink($zip);
            Utils::return_ERROR($e->getMessage());
        } catch (\Throwable $e) {
            @unlink($zip);
            $grav['log']->error('Dotace | generation failed | ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine());
            Utils::return_ERROR('Generování se nezdařilo.');
        }
    }
}
?>
