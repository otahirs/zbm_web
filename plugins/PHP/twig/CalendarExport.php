<?php
namespace Grav\Plugin;
use Grav\Common\Grav;

class CalendarExport extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'CalendarExport';
    }
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('phpCalendarExport', [$this, 'calendarExport']),  
        ];
    }

    public static function calendarExport(){
        $page = Grav::instance()['page'];
        $name = $page->value("folder");
        $collection = $page->evaluate(['@page.descendants' => '/data/events'])->routable();
        if($name != "all") {
            $group = $page->evaluate(['@taxonomy.skupina' => $name]);
            $collection->intersect($group);
        }

        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: atachment; filename=zbm_calendar_'. $name .'.ics');

        echo "BEGIN:VCALENDAR\r\n";
        echo "VERSION:2.0\r\n";
        echo "PRODID:". $page->id() ."/v1/zabiny.club//cs-CZ\r\n";
        echo "CALSCALE:GREGORIAN\r\n";
        echo "X-WR-TIMEZONE:Europe/Prague\r\n";
        echo "X-PUBLISHED-TTL:PT1H\r\n";
        echo "REFRESH-INTERVAL;VALUE=DURATION:P1H\r\n";

        foreach ($collection as $event) {
            echo "BEGIN:VEVENT\r\n";
            echo "UID:". $event->value("header.id") ."\r\n";
            echo "DTSTAMP:". date('Ymd\THis', $event->modified()) ."\r\n";
            echo "DTSTART;VALUE=DATE:". date("Ymd", strtotime($event->value("header.start")))."\r\n";
            echo "DTEND;VALUE=DATE:". date("Ymd", strtotime($event->value("header.end") . "+ 1 day"))."\r\n";
            echo "SUMMARY:". $event->value("header.title") ."\r\n";
            echo "LOCATION:". $event->value("header.place") ."\r\n";
            echo "URL:". $event->url() ."\r\n";
            echo "END:VEVENT\r\n";
        }
        echo "END:VCALENDAR\r\n";
        
    }

}
?>
