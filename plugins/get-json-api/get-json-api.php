<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\GPM\Response;

class GetJSONAPIPlugin extends Plugin
{
    public static function getSubscribedEvents() {
        return [
            'onTwigInitialized' => ['onTwigInitialized', 0]
            ];
    }

    public function onTwigInitialized() {
        $this->grav['twig']->twig()->addFilter(
            new \Twig_SimpleFilter('getJson', [$this, 'getRemoteJson'])
        );
    }

    public function getRemoteJson($url) {
	$body = Response::get($url);
	return json_decode($body, true);
    }
}
