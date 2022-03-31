<?php
namespace Grav\Plugin;
use Grav\Common\Cache;
use Grav\Plugin\Utils;
use Grav\Common\Grav;

require_once(__DIR__ . "/Utils.php");

class MapTheory extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'MapTheory';
    }
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('phpSaveMapT', [$this, 'SaveMapT']),  
            new \Twig_SimpleFunction('phpDeleteMapT', [$this, 'DeleteMapT']), 
        ];
    }

    const MAPTHEORY_DATA_URL = "/data/maptheory";

    public static function SaveMapT(){ 
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST['group']) || empty($_POST['date'])) return;

        if(empty($_FILES["PDF"]["tmp_name"])){
            Utils::return_ERROR("Nahrání PDF souboru se nezdařilo.");
        }

        // init vars
        $mapt_group = $_POST['group'];
        $mapt_file_name = $_POST['date'] . ".pdf" ;
        $page = Grav::instance()['page']->find(self::MAPTHEORY_DATA_URL);
        $mapt_save_path = $page->path() .'/'. $mapt_group;

        //get frontmatter
        $frontmatter = (array)$page->header();

        // add maptheory to frontmatter
        if(isset($frontmatter['maptheory'][$mapt_group]) && in_array ($mapt_file_name , $frontmatter['maptheory'][$mapt_group]) ){
            Utils::return_ERROR("Už je nahrana mapova teorie se stejnym datem a skupinou");
        }
        else{
            $frontmatter['maptheory'][$mapt_group][] = $mapt_file_name;
            arsort($frontmatter['maptheory'][$mapt_group]);
        } 

        // save pdf 
        Utils::save_PDF($mapt_save_path, $mapt_file_name, $makeThumbnail=False);
        
        // save page
        $page->header($frontmatter);
        $page->save();

        Utils::log("MapTheory | saved | {$mapt_file_name}");
        Cache::clearCache('cache-only');
    }

    public static function DeleteMapT(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST['group']) || empty($_POST['name'])) return;

        // init vars
        $mapt_name = $_POST['name'];
        $mapt_group = $_POST['group'];
        $page = Grav::instance()['page']->find(self::MAPTHEORY_DATA_URL);
        $mapt_file_path = $page->path() .'/'. $mapt_group . '/' . $mapt_name;
        
        // get frontmatter
        $frontmatter = (array)$page->header();

        // remove maptheory from frontmatter
        if (($key = array_search($mapt_name, $frontmatter['maptheory'][$mapt_group])) !== false) {
            unset($frontmatter['maptheory'][$mapt_group][$key]);
        }
        
        // delete pdf
        if(file_exists($mapt_file_path)){
            unlink($mapt_file_path);
        }

        // save page
        $page->header($frontmatter);
        $page->save();

        Utils::log("MapTheory | deleted | {$mapt_name}");
        Cache::clearCache('cache-only');
    }

}
?>
