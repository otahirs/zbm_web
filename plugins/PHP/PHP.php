<?php
namespace Grav\Plugin;
use Grav\Common\Cache as Cache;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Grav;
use Grav\Common\Helpers\LogViewer;
use \Grav\Common\Plugin;
class PHPPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onTwigExtensions' => ['onTwigExtensions', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
			'onSchedulerInitialized' => ['onSchedulerInitialized', 0],
        ];
    }

    // add functions to twig so they can be called on pages
    public function onTwigExtensions()
    {
        require_once(__DIR__ . '/twig/PhpTwigExtension.php');
        $this->grav['twig']->twig->addExtension(new PhpTwigExtension());

        require_once(__DIR__ . '/twig/News.php');
        $this->grav['twig']->twig->addExtension(new News());

        require_once(__DIR__ . '/twig/Events.php');
        $this->grav['twig']->twig->addExtension(new Events());

        require_once(__DIR__ . '/twig/WeeklyPlan.php');
        $this->grav['twig']->twig->addExtension(new WeeklyPlan());

        require_once(__DIR__ . '/twig/Polaris.php');
        $this->grav['twig']->twig->addExtension(new Polaris());

        require_once(__DIR__ . '/twig/MapTheory.php');
        $this->grav['twig']->twig->addExtension(new MapTheory());

        require_once(__DIR__ . '/twig/CalendarExport.php');
        $this->grav['twig']->twig->addExtension(new CalendarExport());
    }

    /*************/
    /* Logs page */
    /*************/
    public function onTwigTemplatePaths()
	{
		// add templates to twig path
		$this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }
    public function onTwigSiteVariables()
	{
        // setup
        $twig = $this->grav['twig'];
		$page = $this->grav['page'];

		// only load the vars if logs page
		if ($page->template() == 'logs')
        {		            
            $twig->twig_vars['logviewer'] = new LogViewer();
		}
    }
    
    /********************/
    /* Scheduler (cron) */
    /********************/
    public function onSchedulerInitialized(Event $e)
    {
        require_once(__DIR__ . '/twig/Events.php');
        $scheduler = $e['scheduler'];

        // shift plan to next week every monday at 00:00
        $job = $scheduler->addFunction('\Grav\Plugin\PHPPlugin::shiftPlan2', [], 'shift-plan2-for-this-week');
        $job->at('0 0 * * 1'); 

        // import events from members.eob.cz/zbm/ every 10min
        $job = $scheduler->addFunction('\Grav\Plugin\Events::importRacesFromMembers', [], 'import-races-from-members');
	$job->at('*/10 * * * *'); 
    }


    /*************/
    /* Next week */
    /*************/
    public static function shiftPlan2(){
        require_once(__DIR__ . '/twig/WeeklyPlan.php');

        $page = Grav::instance()['page']->find(WeeklyPlan::PLAN_URL);
        $plan_frontmatter = (array)$page->header();
        $template_frontmatter = (array)$page->find(WeeklyPlan::PLAN_TEMPLATES_URL)->header();

        $default_template = $template_frontmatter["defaultTemplate"];
        $plan_frontmatter["plan"]["thisWeek"] = $plan_frontmatter["plan"]["nextWeek"];
        $plan_frontmatter["plan"]["nextWeek"] = $template_frontmatter["templates"][$default_template];

        $page->header($plan_frontmatter);
        $page->save();

        Grav::instance()['log']->info("Plan2 shifted");

        Cache::clearCache('cache-only');
    }



    

}
?>
