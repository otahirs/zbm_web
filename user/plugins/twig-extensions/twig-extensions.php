<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class TwigExtensionsPlugin
 * @package Grav\Plugin
 */
class TwigExtensionsPlugin extends Plugin
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
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onTwigExtensions' => ['onTwigExtensions', -100],
        ]);
    }

    public function onTwigExtensions()
    {
        $modules = $this->grav['config']->get('plugins.twig-extensions.modules');
        if (in_array('intl', $modules)) {
            require_once(__DIR__ . '/vendor/Twig/Intl.php');
            $this->grav['twig']->twig->addExtension(new \Twig_Extensions_Extension_Intl());
        }
        if (in_array('array', $modules)) {
            require_once(__DIR__ . '/vendor/Twig/Array.php');
            $this->grav['twig']->twig->addExtension(new \Twig_Extensions_Extension_Array());
        }
        if (in_array('date', $modules)) {
            require_once(__DIR__ . '/vendor/Twig/Date.php');
            $this->grav['twig']->twig->addExtension(new \Twig_Extensions_Extension_Date());
        }
    }
}
