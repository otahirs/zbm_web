<?php
namespace Grav\Plugin;
use Grav\Common\Cache;
use Grav\Plugin\Utils;
use Grav\Common\Grav;

require_once(__DIR__ . "/Utils.php");

class Polaris extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'Polaris';
    }
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('phpSavePolaris', [$this, 'SavePolaris']),
            new \Twig_SimpleFunction('phpDeletePolaris', [$this, 'DeletePolaris']),
        ];
    }

    const POLARIS_DATA_URL = "/data/polaris";

    public static function SavePolaris(){ 
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if(empty($_POST['year']) || empty($_POST['cislo'])) return;

        if(empty($_FILES["PDF"]["tmp_name"])){
            Utils::return_ERROR("Nahrání PDF souboru se nezdařilo.");
        }
        
        // init vars
        $polaris_year = $_POST['year'];
        $polaris_number = "p" . $_POST['cislo'];
        $pdf_file_name = "Polaris_" . $_POST['year'] . "_" . $_POST['cislo'] . ".pdf" ;
        $page = Grav::instance()['page']->find(self::POLARIS_DATA_URL);
        $pdf_save_path = $page->path() . "/" . $_POST['year'];
       
        //get frontmatter
        $frontmatter = (array)$page->header();

        // add polaris to frontmatter
        if(isset($frontmatter['polaris'][$polaris_year]) && in_array($pdf_file_name, $frontmatter['polaris'][$polaris_year])){
            Utils::return_ERROR("Už je nahrané stejné číslo Polarisu.");
        }

        $frontmatter['polaris'][$polaris_year][$polaris_number] = $pdf_file_name;
        krsort($frontmatter['polaris']);
        krsort($frontmatter['polaris'][$polaris_year]);

        // save pdf and jpeg thumbnail
        Utils::save_PDF($pdf_save_path, $pdf_file_name);
        
        $page->header($frontmatter);
        $page->save();

        Utils::log("Polaris | saved | {$pdf_file_name}");
        Cache::clearCache('cache-only');
    }

    public static function DeletePolaris(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if (empty($_POST['year']) || empty($_POST['cislo']) || empty($_POST['pdf'])) return;

    
        // init vars
        $polaris_year = $_POST['year'];
        $polaris_number = "p" . $_POST['cislo'];
        $page = Grav::instance()['page']->find(self::POLARIS_DATA_URL);
        $pdf_file_path = $page->path() . "/" . $_POST['year']. "/" . $_POST['pdf'];
        
        // get frontmatter
        $frontmatter = (array)$page->header();

        // remove polaris from frontmatter
        unset($frontmatter['polaris'][$polaris_year][$polaris_number]);

        // delete pdf and jpeg thumbnail
        if(file_exists($pdf_file_path)){
            unlink($pdf_file_path);
        }
        if(file_exists($pdf_file_path .".jpg")){
            unlink($pdf_file_path .".jpg");
        }

        // remove years if year is empty
        $frontmatter['polaris'] = array_filter($frontmatter['polaris']);

        // build page
        $page->header($frontmatter);
        $page->save();

        Utils::log("Polaris | deleted | {$_POST['pdf']}");
        Cache::clearCache('cache-only');
    }

}
?>
