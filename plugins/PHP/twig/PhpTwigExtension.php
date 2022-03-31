<?php
namespace Grav\Plugin;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Cache as Cache;
use Grav\Common\GPM\Response;
use Grav\Plugin\Utils;
require_once(__DIR__ . "/Utils.php");

class PhpTwigExtension extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'PhpTwigExtension';
    }
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('phpTest', [$this, 'Test']),        
            new \Twig_SimpleFunction('collectionToEventsByDate', [$this, 'collectionToEventsByDate']), 
        
        ];
    }
    
    static function Test() {
        echo date("Y-m-d", strtotime(str_replace(' ','', "7. 9. 2020")));
    }
       
    public static function collectionToEventsByDate($collection){
        $array = array();
        foreach($collection as $event) {
            $date = $event->value("header.start");
            $today = date('Y-m-d');
            if ($date < $today) {
                $date = $today;
            }
            $array[$date][] = $event;
        }
        return $array;
    }

}

?>
