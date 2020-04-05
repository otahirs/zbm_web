<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use Grav\Plugin\Views\Views;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class ViewsPlugin
 * @package Grav\Plugin
 */
class ViewsPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onCliInitialize' => [
                ['autoload', 100000],
                ['register', 1000]
            ],
            'onPluginsInitialized' => [
                ['autoload', 100000],
                ['register', 1000],
                ['onPluginsInitialized', 1000]
            ]
        ];
    }

    /**
     * [onPluginsInitialized:100000] Composer autoload.
     *
     * @return ClassLoader
     */
    public function autoload()
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Register the service
     */
    public function register()
    {
        $this->grav['views'] = function ($c) {
            return new Views($c['config']->get('plugins.views'));
        };
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminGenerateReports' => [
                    ['onAdminGenerateReports', 10]
                ],
                'onTwigTemplatePaths' => [
                    ['onTwigTemplatePaths', 0]
                ],
            ]);
            return;
        }

        $this->enable([
            'onTwigInitialized' => [
                ['onTwigInitialized', 0]
            ],
        ]);

        if ($this->grav['config']->get('plugins.views.autotrack')) {
            $this->enable([
                'onPageInitialized' => [
                    ['onPageInitialized', 0]
                ],
            ]);
        }
    }

    public function onAdminGenerateReports(Event $e)
    {
        $reports = $e['reports'];

        /** @var Uri $uri */
        $uri = $this->grav['uri'];

        $data = [
            'views' => $this->grav['views']->getAll(null, 20, 'desc'),
            'base_url' => $baseUrlRelative = $uri->rootUrl(false),
        ];

        $reports['Grav Views'] = $this->grav['twig']->processTemplate('reports/views-report.html.twig', $data);

        $this->grav['assets']->addCss('plugins://views/css/admin.css');
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }


    public function onTwigInitialized()
    {
        $this->grav['twig']->twig()->addFunction(
            new \Twig_SimpleFunction('track_views', [$this, 'trackViewsFunc'], ['is_safe' => ['html']])
        );
    }

    public function onPageInitialized(Event $event)
    {
        $page = $event['page'];

        $this->grav['views']->track($page->route(), 'pages');
    }

    /**
     * @param mixed|null $id
     */
    public function trackViewsFunc($id = null, $type = 'pages')
    {
        if (null === $id) {
            return;
        }

        // Convert objects to string
        $id = (string)$id;

        $this->grav['views']->track($id, $type);
    }
}
