<?php
namespace Grav\Plugin;
use Grav\Common\Cache;
use Grav\Plugin\Utils;
use Grav\Common\Grav;

require_once(__DIR__ . "/Utils.php");

class WeeklyPlan extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'WeeklyPlan';
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('phpSavePlan2', [$this, 'SavePlan2']),
            new \Twig_SimpleFunction('phpLoadPlanFromTemplate', [$this, 'LoadPlanFromTemplate']),
            new \Twig_SimpleFunction('phpSavePlan2Template', [$this, 'SavePlan2Template']),
            new \Twig_SimpleFunction('phpCreatePlanTemplate', [$this, 'CreatePlanTemplate']),
            new \Twig_SimpleFunction('phpDeletePlanTemplate', [$this, 'DeletePlanTemplate']),
            new \Twig_SimpleFunction('phpSetDefaultTemplate', [$this, 'SetDefaultTemplate']),
        ];
    }

    const PLAN_URL = "/auth/plan2/";
    const PLAN_TEMPLATES_URL = "/auth/plan2/templates";

    static function SavePlan2Template(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["template"])) return;

        $template_name = key($_POST["template"]);
        $template = $_POST["template"][$template_name];

        $page = Grav::instance()['page']->find(self::PLAN_TEMPLATES_URL);
        $frontmatter = (array)$page->header();
        $frontmatter["templates"][$template_name] = $template;

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplate | edited | {$template_name}");
        Cache::clearCache('cache-only');
    }

    static function edit_events_groups($events) {
        foreach($events as $id => $groups) {
            $year = substr($id, 0, 4);
            $page_url = "/data/events/{$year}/{$id}";

            $page = Grav::instance()['page']->find($page_url);
            $frontmatter = (array)$page->header();
            $frontmatter["taxonomy"]["skupina"] = $groups;

            $page->header($frontmatter);
            $page->save();
        }
    }

    public static function SavePlan2(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["plan"])) return;

        $page = Grav::instance()['page']->find(self::PLAN_URL);
        $frontmatter = (array)$page->header();
        $frontmatter["plan"] = $_POST["plan"];

        if (!empty($_POST["events"])) {
            self::edit_events_groups($_POST["events"]);
        }

        $page->header($frontmatter);
        $page->save();

        Utils::log("Plan | edited");
        Cache::clearCache('cache-only');
    }

    public static function LoadPlanFromTemplate(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["week"]) || empty($_POST["template"])) return;

        $page = Grav::instance()['page']->find(self::PLAN_URL);
        $plan_frontmatter = (array)$page->header();
        $template_frontmatter = (array)$page->find(self::PLAN_TEMPLATES_URL)->header();

        $week = $_POST["week"];
        $template = $_POST["template"];
        $plan_frontmatter["plan"][$week] = $template_frontmatter["templates"][$template];

        $page->header($plan_frontmatter);
        $page->save();

        Utils::log("Plan | loaded from template | {$_POST["week"]} => {$_POST["template"]}");
        Cache::clearCache('cache-only');
    }

    public static function DeletePlanTemplate() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["deletedTemplate"])) return;
        $del_template = $_POST["deletedTemplate"];

        $page = Grav::instance()['page']->find(self::PLAN_TEMPLATES_URL);
        $frontmatter = (array)$page->header();
        unset($frontmatter["templates"][$del_template]);

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplate | deleted | {$del_template}");
        Cache::clearCache('cache-only');
    }

    public static function SetDefaultTemplate() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["defaultTemplate"])) return;

        $page = Grav::instance()['page']->find(self::PLAN_TEMPLATES_URL);
        $frontmatter = (array)$page->header();
        $frontmatter["defaultTemplate"] = $_POST["defaultTemplate"];

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplate | set default | {$_POST["defaultTemplate"]}");
        Cache::clearCache('cache-only');
    }

    public static function CreatePlanTemplate() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST["templateName"])) return;
        $template_name = Utils::trim_all($_POST["templateName"]);
        $page = Grav::instance()['page']->find(self::PLAN_TEMPLATES_URL);
        $frontmatter = (array)$page->header();

        $counter = 0;
        $new_template_name = $template_name;
        while (array_key_exists($new_template_name, $frontmatter["templates"])) {
            $counter++;
            $new_template_name = $template_name . "(". $counter .")";
        }

        $frontmatter["templates"][$new_template_name] = array();

        $page->header($frontmatter);
        $page->save();

        Utils::log("PlanTemplate | created | {$template_name}");
        Cache::clearCache('cache-only');
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($new_template_name);
    }

}
?>
