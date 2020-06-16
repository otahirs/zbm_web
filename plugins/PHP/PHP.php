<?php
namespace Grav\Plugin;
use Symfony\Component\Yaml\Yaml as Yaml;
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
    public function onTwigExtensions()
    {
        require_once(__DIR__ . '/twig/PhpTwigExtension.php');
        $this->grav['twig']->twig->addExtension(new PhpTwigExtension());
    }

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

		// only load the vars if calendar page
		if ($page->template() == 'logs')
        {			// add calendar to twig as calendar
            
            $twig->twig_vars['logviewer'] = new LogViewer();
		}


    }
    
    public function onSchedulerInitialized(Event $e)
    {
        require_once(__DIR__ . '/twig/PhpTwigExtension.php');
        $scheduler = $e['scheduler'];

        $job = $scheduler->addFunction('\Grav\Plugin\PHPPlugin::shiftPlan2', [], 'shift-plan2-for-this-week');
        $job->at('0 0 * * 1'); // every monday at 00:00
        
        $job = $scheduler->addFunction('Grav\Plugin\PhpTwigExtension::importRacesFromMembers', [], 'import-races-from-members');
        $job->at('0 0 * * *'); 
    }


    public static function shiftPlan2(){
        require_once(__DIR__ . '/twig/PhpTwigExtension.php');
        $php = new PhpTwigExtension();

        $plan_path = "./user/pages/auth/plan2/blank.md";
        $plan_frontmatter = $php->get_frontmatter_as_array($plan_path);
        $template_frontmatter = $php->get_frontmatter_as_array("./user/pages/auth/plan2/templates/blank.md");

        $default_template = $template_frontmatter["defaultTemplate"];
        $plan_frontmatter["plan"]["thisWeek"] = $plan_frontmatter["plan"]["nextWeek"];
        $plan_frontmatter["plan"]["nextWeek"] = $template_frontmatter["templates"][$default_template]["plan"];

        $php->save_page_with_edited_frontmatter($plan_path, $plan_frontmatter);

        $php->log_grav("Plan2 shifted");

        Cache::clearCache('cache-only');        
    }



    

}
?>
