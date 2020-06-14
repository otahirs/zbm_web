<?php
namespace Grav\Plugin;
use Symfony\Component\Yaml\Yaml as Yaml;
use Grav\Common\Grav;
use Grav\Common\Cache as Cache;
use Grav\Common\GPM\Response;
class PhpTwigExtension extends \Grav\Common\Twig\TwigExtension
{
    public function getName()
    {
        return 'PhpTwigExtension';
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('api_racelist', [$this, 'api_racelist']),
            new \Twig_SimpleFunction('phpUploadProgram', [$this, 'phpUploadProgram']),
            new \Twig_SimpleFunction('phpNews', [$this, 'NewsFunction']),
            new \Twig_SimpleFunction('phpEditBliziSe', [$this, 'editBliziSeFunction']),
            new \Twig_SimpleFunction('phpSaveProgramTemplates', [$this, 'SaveProgramTemplates']),
            new \Twig_SimpleFunction('phpSaveEditedEvent', [$this, 'phpSaveEditedEvent']),
            new \Twig_SimpleFunction('phpSavePolaris', [$this, 'SavePolaris']),
            new \Twig_SimpleFunction('phpDeletePolaris', [$this, 'DeletePolaris']),
            new \Twig_SimpleFunction('phpSavePlan', [$this, 'SavePlan']),
            new \Twig_SimpleFunction('phpSavePlan2', [$this, 'SavePlan2']),
            new \Twig_SimpleFunction('phpLoadPlanFromTemplate', [$this, 'LoadPlanFromTemplate']),
            new \Twig_SimpleFunction('phpSavePlanTemplate', [$this, 'SavePlanTemplate']), 
            new \Twig_SimpleFunction('phpSavePlan2Template', [$this, 'SavePlan2Template']),
            new \Twig_SimpleFunction('phpCreatePlanTemplate', [$this, 'CreatePlanTemplate']),
            new \Twig_SimpleFunction('phpDeletePlanTemplate', [$this, 'DeletePlanTemplate']),
            new \Twig_SimpleFunction('phpSetDefaultTemplate', [$this, 'SetDefaultTemplate']),
            new \Twig_SimpleFunction('phpTest', [$this, 'Test']),      
            new \Twig_SimpleFunction('phpSaveMapT', [$this, 'SaveMapT']),  
            new \Twig_SimpleFunction('phpDeleteMapT', [$this, 'DeleteMapT']),    
            new \Twig_SimpleFunction('collectionToEventsByDate', [$this, 'collectionToEventsByDate']), 
            new \Twig_SimpleFunction('phpCalendarExport', [$this, 'calendarExport']),  
        
        ];
    }
    
    function calendarExport(){
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
   
// pomocne fce
    static function return_ERROR($errMsg){
        http_response_code(500); // 500 - Internal server error
        echo $errMsg;
        die();
    }

    function log_grav($msg){
        Grav::instance()['log']->info($msg);
    }

    /*************************************************************
    ** projde vsechny prvky array a aplikuje htmlspecialchars() **
    **************************************************************/
    function array_htmlspecialchars(&$array){
        array_walk_recursive($array, function(&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        });
    }

    /**********************************************
    ** zapise soubor i kdyz chybi v ceste slozka **
    **********************************************/
    function file_force_contents($dir, $contents){
        $parts = explode("/", $dir);
        $file = array_pop($parts);
        $dir = "";

        foreach($parts as $part) {
            if (! is_dir($dir .= "{$part}/")) mkdir($dir);
        }
        //$contents = htmlspecialchars($contents, ENT_NOQUOTES, 'UTF-8');
        return file_put_contents("{$dir}{$file}", $contents);
    }


    /******************************************************
    **      odstihne singlequotes, doublequotes          **
    ** a ze zacatku a konce stringu vsechny bile znakya  **
    ******************************************************/
    function trim_all($str){
        $str = str_replace( "'" , "" , $str );
        $str = str_replace( '"' , "" , $str );
        $str = trim($str);
        $str = htmlspecialchars(html_entity_decode($str, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        $str = str_replace( "\n" , "<br>" , $str );
        return $str;
    }

    static function startsWith ($string, $startString) { 
        $len = strlen($startString); 
        return (substr($string, 0, $len) === $startString); 
    } 

    function format_date($date){
        $newdate = date_create_from_format('d.m.Y', $date);
        if($newdate){
            return date_format($newdate, 'Y-m-d');
        }
        else{
            return $date;
        }
    }

    function get_event_template($event){
        if(isset($event)){
            switch ($event) {
                case "Z":
                case "BZL":
                  $template = "zavod";        break;
                case "M":
                case "T":
                case "BBP":
                  $template = "trenink";      break;
                case "S":
                case "TABOR":
                  $template = "soustredeni";    break;
                //case "TABOR":
                // $template = "tabor";    break;
                default:
                  $template = "akce";
              }
        }
        return $template;
    }

    function rcopy($src, $dst) {  
        // open the source directory 
        $dir = opendir($src);  
        // Make the destination directory if not exist 
        @mkdir($dst);  
        // Loop through the files in source directory 
        while( $file = readdir($dir) ) {
            if (( $file != '.' ) && ( $file != '..' )) {  
                if ( is_dir($src . '/' . $file) )  
                {  
                    // Recursively calling custom copy function 
                    // for sub directory  
                    PhpTwigExtension::rcopy($src . '/' . $file, $dst . '/' . $file);  
      
                }  
                else {  
                    copy($src . '/' . $file, $dst . '/' . $file);  
                }  
            }  
        }  
        closedir($dir); 
    }  

    /**********************************************
    **      fce pro parsovani stranek            **
    **********************************************/
    
    function parse_file_frontmatter_only($path_to_file){
        if(!file_exists($path_to_file)){
            PhpTwigExtension::return_ERROR('Cannot parse file, "'. $path_to_file. '" do not match any file.');
        }
        $txt_file    = file_get_contents($path_to_file); //nacte soubor
        $rows        = explode("\n", $txt_file); //rozdeli na radky
        array_shift($rows); //odstrani prvni radek souboru obsahujici "---"
        $parsed = "";
        foreach($rows as $row){   //prochazi vsechny radky
            if(trim($row) == "---"){
                break;
            }
            $parsed .= $row . PHP_EOL;
        }
        return $parsed;
    }
    
    function parse_file_content_only($path_to_file){
        if(!file_exists($path_to_file)){
            PhpTwigExtension::return_ERROR('Cannot parse file, "'. $path_to_file. '" do not match any file.');
        }
        $txt_file    = file_get_contents($path_to_file); //nacte soubor
        $rows        = explode("\n", $txt_file); //rozdeli na radky
        array_shift($rows); //odstrani prvni radek souboru obsahujici "---"

        $row_is_content = false;
        $parsed = "";
        foreach($rows as $key => $row){   //prochazi vsechny radky
            if($row_is_content){
                $parsed .= $row; 
                if($key !== array_key_last($rows)) $parsed .= PHP_EOL;
            }
            else{
                if(trim($row) == "---"){
                    $row_is_content = true;
                }
            }           
        }
        return $parsed;
    }
    
    function combine_frontmatter_with_content($frontmatter, $content){
        $page = "---" . PHP_EOL;
        $page .= $frontmatter;
        $page .= "---" . PHP_EOL;
        $page .= $content;
        return $page;
    }

    function get_frontmatter_as_array($path_to_file){
        $frontmatter_yaml = PhpTwigExtension::parse_file_frontmatter_only($path_to_file);
        return Yaml::parse($frontmatter_yaml);
        // https://symfony.com/doc/current/components/yaml.html 
    }

    function save_page_with_edited_frontmatter($path, $frontmatter) {
        // get page content
        $content = $this->parse_file_content_only($path);

        // arr to string
        if (is_array($frontmatter)) {
            $frontmatter = Yaml::dump($frontmatter, 10);
        }
        // build page
        $page = $this->combine_frontmatter_with_content($frontmatter, $content);
        
        // save page
        $this->file_force_contents($path, $page);
    }

    function generate_content($race){
        $content = "";
        // zapise uvod pro ligu škol popř. pořádáme
        if(!empty($race["Type"])){
            if($race["Type"]=="L"){
                $content .= "**Pořádáme!!** Předem díky moc za pomoc s pořádáním. Kdo má čas nebo by chtěl
                omluvit ze školy, hlaste se Liborovi." . PHP_EOL;
            }
            else{
                if(!empty($race["doWeOrganize"])){
                    if($race["doWeOrganize"]=="1"){
                        $content .= "**Pořádáme!!**" . PHP_EOL;
                    }
                }
            }
        }
        if(!empty($race["note"]))                {$content .= "{{page.header.note}}" . PHP_EOL;}
        // jeden řádek s časem, místem srazu a typem dopravy
        if(!empty($race["meetTime"]))            {$content .= "* **sraz**: {{page.header.meetTime}}"; $writw_eol=true;}
        if(!empty($race["meetPlace"]))           {$content .= " {{page.header.meetPlace}}."; $writw_eol=true;}
        if(!empty($race["transport"]))           {$content .= " Doprava {{page.header.transport}}."; $writw_eol=true;}
        if(isset($writw_eol)) {if($writw_eol==true) $content .= PHP_EOL;}

        if(!empty($race["accomodation"]))        {$content .= "* **ubytování**: {{page.header.accomodation}}" . PHP_EOL;}
        if(!empty($race["food"]))                {$content .= "* **strava**: {{page.header.food}}" . PHP_EOL;}
        return $content;
      }

     
      /*******************************************************
      **  funkce, ktera nacte JSON data z api, pokud zavod  **
      **  neexistuje, je vytvoren, pokud existuje, jsou     **
      **  rozdilna data aktualizovana z clenske sekce       **
      ********************************************************

      function api_racelist(){

        $api = file_get_contents("https://members.eob.cz/tst/api_racelist.php"); //nahraje JSON do stringu
        $json = json_decode($api, true, JSON_UNESCAPED_UNICODE); //a dekoduje ho vcetne UTF8 znaků

        if($json["Status"]=="OK"){ //pokud byl dotaz na api uspesny
          foreach ($json["Data"] as $id => $data_json) { // prochazi kazdou akci
            
          }
        }
        else{ mail("otakar.hirs@egmail.com","JSON error zbmob.cz", "Nacteni dat z clenske sekce pres JSON neskoncilo OK, mel bys to asi zkontrolovat. \n Ota - 2018"); }

      }*/




//******************************************************************************************************/
//pridat nebo upravit Novinku



/************************************************************
** funkce, ktera ulozi novinku jako soubor **
*************************************************************/

    function news_to_file($data, $year){
      $news = "---" . PHP_EOL .
              "title: '" . str_replace("'","''",$data['title']) . "'" . PHP_EOL . // escape ' in frontmatter
              "date: '" . $data['date'] . "'" . PHP_EOL .
              "template: novinka" . PHP_EOL .
              "id: '" . $data['id'] . "'" . PHP_EOL .
              "user: '" . $data['User'] . "'" . PHP_EOL .
              "pictures:" . PHP_EOL;

              if(isset($data['img'])){
                foreach ($data['img'] as $img) {
                  if(isset($img['img_delete'])){
                    if($img['img_delete'] == "true"){
                      $imgPath =     "./user/pages/data/news/" . $year . "/". $data['id'] . "/img/" . $img['img_name'];
                      $previewPath = "./user/pages/data/news/" . $year . "/". $data['id'] . "/img/" . $img['img_name'] . "_preview.jpg";
                      if (file_exists($imgPath)) unlink($imgPath);
                      if (file_exists($previewPath)) unlink($previewPath);
                      continue;
                    }
                  }
                  $news .=  "    - name: '" . $img['img_name'] . "'" . PHP_EOL .
                            "      ratio: '". $img['img_ratio'] ."'" . PHP_EOL;
                }
              }
     $news .= "---" . PHP_EOL .
              $data['content'] . PHP_EOL;

        $news = htmlspecialchars($news, ENT_NOQUOTES, 'UTF-8');
        //probehne vytvoreni slozky a ulozeni souboru
        PhpTwigExtension::file_force_contents("./user/pages/data/news/" . $year . "/". $data['id'] . "/default.cs.md", $news);
    }

    /******************************************************
     **********   Create thumbnail          **************
    ***************************************************** */
    // link image type to correct image loader and saver
    // - makes it easier to add additional types later on
    // - makes the function easier to read
    const IMAGE_HANDLERS = [
        IMAGETYPE_JPEG => [
            'load' => 'imagecreatefromjpeg',
        ],
        IMAGETYPE_PNG => [
            'load' => 'imagecreatefrompng',
        ],
        IMAGETYPE_GIF => [
            'load' => 'imagecreatefromgif',
        ]
    ];

    /**
     * @param $src - a valid file location
     * @param $dest - a valid file target
     * @param $targetWidth - desired output width
     * @param $targetHeight - desired output height or null
     */
    function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {

        // 1. Load the image from the given $src
        // - see if the file actually exists
        // - check if it's of a valid image type
        // - load the image resource

        // get the type of the image
        // we need the type to determine the correct loader
        $type = exif_imagetype($src);

        // if no valid type or no handler found -> exit
        if (!$type || !self::IMAGE_HANDLERS[$type]) {
            return null;
        }

        // load the image with the correct loader
        $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $src);

        // no image found at supplied location -> exit
        if (!$image) {
            return null;
        }


        // 2. Create a thumbnail and resize the loaded $image
        // - get the image dimensions
        // - define the output size appropriately
        // - create a thumbnail based on that size
        // - set alpha transparency for GIFs and PNGs
        // - draw the final thumbnail

        // get original image width and height
        $width = imagesx($image);
        $height = imagesy($image);

        // maintain aspect ratio when no height set
        if ($targetHeight == null) {

            // get width to height ratio
            $ratio = $width / $height;

            // if is portrait
            // use ratio to scale height to fit in square
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            }
            // if is landscape
            // use ratio to scale width to fit in square
            else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        // create duplicate image based on calculated target size
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

            // make image transparent
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );

            // additional settings for PNGs
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        // copy entire source image to duplicate image and resize
        imagecopyresampled(
            $thumbnail,
            $image,
            0, 0, 0, 0,
            $targetWidth, $targetHeight,
            $width, $height
        );


        // 3. Save the $thumbnail to disk
        // - set the correct quality level

        return imagejpeg($thumbnail, $dest, 70);
    }


    function process_files($id, $previewWidthInPx, $year){
    
        $storeFolder = "./user/pages/data/news/" . $year . "/". $id . "/img/";

        $extension=array("jpeg","jpg","png","gif","JPEG","JPG","PNG","GIF","jpe","jif","jfif","jfi","JPE","JIF","JFIF","JFI"); //.jpe .jif, .jfif, .jfi jsou soubory jpeg

        foreach($_FILES["file"]["tmp_name"] as $key=>$tmp_name){
            $file_name=$_FILES["file"]["name"][$key];
            $ext=pathinfo($file_name,PATHINFO_EXTENSION);
            if(!in_array($ext,$extension))
            {
                PhpTwigExtension::return_ERROR("<em>" . $file_name . "</em>není podporovaný typ obrázku");
            }
                
        }

        foreach($_FILES["file"]["tmp_name"] as $key=>$tmp_name){

            $file_name=$_FILES["file"]["name"][$key];
            $file_tmp=$_FILES["file"]["tmp_name"][$key];
            
            if(!file_exists($storeFolder . $file_name)){
                if(! is_dir($storeFolder)){
                    mkdir($storeFolder);
                }
                $saveImagePath = $storeFolder . $file_name;
                $savePreviewPath = $saveImagePath . "_preview.jpg";
                move_uploaded_file($file_tmp=$_FILES["file"]["tmp_name"][$key], $saveImagePath);
                PhpTwigExtension::createThumbnail($saveImagePath, $savePreviewPath, $previewWidthInPx, $targetHeight = null);
            };
        }
    }

    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") PhpTwigExtension::rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    function save_news($user, $id, $date, $year){
        $data['title'] = $_POST["title"];
        $data['id'] = $id;
        $data['User'] = $user;
        $data['date'] = $date;
        $data['content'] = $_POST['content'];
        if(isset($_POST['img'])){
            $data['img'] = $_POST['img'];
        }

        PhpTwigExtension::news_to_file($data, $year);
        if (!empty($_FILES)) {
            PhpTwigExtension::process_files($data['id'], 1000, $year);
        }
        
        
    }

    public function NewsFunction($user){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST["POST_type"])){
                if( $_POST["POST_type"] == "addNews" ){
                    $id = date("Ymd-Hisv");
                    $date = date("Y-m-d");
                    $year = substr($date, 0 , 4);
                    PhpTwigExtension::save_news($user, $id, $date, $year);
                    PhpTwigExtension::log_grav($user . " | NEWS created | " . $id);
                }
                elseif( $_POST["POST_type"] == "updateNews" ){
                    $id = $_POST["id"];
                    $date = date( "Y-m-d", strtotime(str_replace(' ','', $_POST["date"])) );
                    $year = substr($date, 0 , 4);
                    $author = $_POST["author"];
                    PhpTwigExtension::save_news($author, $id, $date, $year);
                    PhpTwigExtension::log_grav($user . " | NEWS edited | " . $id);
                }
                elseif( $_POST["POST_type"] == "deleteNews" ){
                    $year = substr($_POST["id"], 0 , 4);
                    PhpTwigExtension::rrmdir("./user/pages/data/news/" . $year . "/". $_POST['id'] . "/");
                    PhpTwigExtension::log_grav($user . " | NEWS removed | " . $_POST["id"]);
                }
                Cache::clearCache('cache-only');
            }
        }
    }

    //******************************************************************************************************/
    //updatuje kontent zobrazovany v Blizi se
    public function editBliziSeFunction($user){
        $year = substr($_POST["id"], 0, 4);
        $path = "./user/pages/data/events/". $year ."/". $_POST["id"] ."/event.cs.md";

        $frontmatter = PhpTwigExtension::parse_file_frontmatter_only($path);
        
        if(isset($_POST["regenerate"]) && $_POST["regenerate"]){
           
            $content = PhpTwigExtension::generate_content(Yaml::parse($frontmatter));
            
        }
        else{
            $content = $_POST["content"];
        }

        $page = PhpTwigExtension::combine_frontmatter_with_content($frontmatter, $content);

        file_put_contents($path, $page);
        Cache::clearCache('cache-only');
    }

    /********************************************************************************
    *********************  plan pravidelnych treninku *******************************
    ********************************************************************************/
    

    function get_plan_template($path_to_file){
        $frontmatter = PhpTwigExtension::get_frontmatter_as_array($path_to_file);
        $template = $frontmatter["planTemplate"];
        return $template;
    }

    function add_season_to_string($season,$data){
        $week = ["monday","tuesday","wednesday","thursday","friday","saturday", "sunday"];
        $last_day_printed = 0;
        foreach ($_POST[$season] as $day_num => $day){
            while($day_num > $last_day_printed){
                $data .= "    ".$week[$last_day_printed].": null". PHP_EOL;
                $last_day_printed++;
            }
            $last_day_printed++;
            $data .= "    ".$week[$day_num].":". PHP_EOL;
            $i = 1;
            foreach ($day as $event){
                $data .= "        " . $i .":" . PHP_EOL .
                        "            name: '"  . $event["name"] ."'". PHP_EOL .
                        "            place: '"  . $event["place"] ."'". PHP_EOL .
                        "            meetup: '"  . $event["meetup"] ."'". PHP_EOL .
                        "            group:". PHP_EOL;
                if(!empty($event["group"])) {
                foreach($event["group"] as $group){
                    $data .= "                    - " . $group . PHP_EOL;
                }
                }
                $i++;
            }
        }
        while($last_day_printed < 7){
            $data .= "    ".$week[$last_day_printed].": null". PHP_EOL;
            $last_day_printed++;
        }
        return $data;
    }

    // ulozit sablony
    function SaveProgramTemplates(){
        $data = "---" . PHP_EOL .
                "title: 'Týdenní program'" . PHP_EOL .
                "date: '2018-09-29'" . PHP_EOL .
                "process:". PHP_EOL .
                "    twig: true" . PHP_EOL .
                "    markdown: false" . PHP_EOL .
                "access:" . PHP_EOL .
                "    site:" . PHP_EOL .
                "        plan: true" . PHP_EOL .
                "currentSeason: " . $_POST["season"]  . PHP_EOL .
                "summer:" . PHP_EOL;
        if(isset($_POST['summer'])){
            $data = PhpTwigExtension::add_season_to_string("summer",$data);
        }
        $data .= "winter:" . PHP_EOL;
        if(isset($_POST['winter'])){
            $data = PhpTwigExtension::add_season_to_string("winter",$data);
        }
        $data .= "---" . PHP_EOL;
        $data .= PhpTwigExtension::parse_file_content_only($_POST["filePath"]);

        PhpTwigExtension::file_force_contents($_POST["filePath"], $data);
        Cache::clearCache('cache-only');
               
    }
/********************************************************
***** tento tyden, pristi tyden / plan, plan-next *******
*********************************************************/

function SavePlan2Template(){
    if (empty($_POST["template"])) return;
    $path = "./user/pages/auth/plan2/templates/blank.md";


    $template_name = key($_POST["template"]);
    $template = $_POST["template"][$template_name];

    $frontmatter = $this->get_frontmatter_as_array($path);             
    $frontmatter["templates"][$template_name] = $template;                 

    $this->save_page_with_edited_frontmatter($path, $frontmatter);
    Cache::clearCache('cache-only');
}

function edit_events_groups($events) {
    foreach($events as $id => $groups) {
        $year = substr($id, 0, 4);
        $path = "./user/pages/data/events/". $year ."/". $id ."/event.cs.md";

        $frontmatter = $this->get_frontmatter_as_array($path);
        $frontmatter["taxonomy"]["skupina"] = $groups;

        $this->save_page_with_edited_frontmatter($path, $frontmatter);
    }
}

function SavePlan2(){
    if (empty($_POST["plan"])) return;
    $path = "./user/pages/auth/plan2/blank.md";
    $frontmatter = $this->get_frontmatter_as_array($path);
    $frontmatter["plan"] = $_POST["plan"];

    if (!empty($_POST["events"])) {
        $this->edit_events_groups($_POST["events"]);
    }

    $this->save_page_with_edited_frontmatter($path, $frontmatter);
    Cache::clearCache('cache-only');
}

function LoadPlanFromTemplate(){
    if (empty($_POST["week"]) || empty($_POST["template"])) return;
    $plan_path = "./user/pages/auth/plan2/blank.md";
    $plan_frontmatter = $this->get_frontmatter_as_array($plan_path);
    $template_frontmatter = $this->get_frontmatter_as_array("./user/pages/auth/plan2/templates/blank.md");

    $week = $_POST["week"];
    $template = $_POST["template"];
    $plan_frontmatter["plan"][$week] = $template_frontmatter["templates"][$template];

   $this->save_page_with_edited_frontmatter($plan_path, $plan_frontmatter);
   Cache::clearCache('cache-only');
}

// function ShiftPlan2() {
//     $plan_path = "./user/pages/auth/plan2/blank.md";
//     $plan_frontmatter = $this->get_frontmatter_as_array($plan_path);
//     $template_frontmatter = $this->get_frontmatter_as_array("./user/pages/auth/plan2/templates/blank.md");

//     $default_template = $template_frontmatter["defaultTemplate"];
//     $plan_frontmatter["plan"]["thisWeek"] = $plan_frontmatter["plan"]["nextWeek"];
//     $plan_frontmatter["plan"]["nextWeek"] = $template_frontmatter["templates"][$default_template]["plan"];

//    $this->save_page_with_edited_frontmatter($plan_path, $plan_frontmatter);
// }

function DeletePlanTemplate() {
    if (empty($_POST["deletedTemplate"])) return;
    $del_template = $_POST["deletedTemplate"];

    $path = "./user/pages/auth/plan2/templates/blank.md";
    $frontmatter = $this->get_frontmatter_as_array($path);

    unset($frontmatter["templates"][$del_template]);

    $this->save_page_with_edited_frontmatter($path, $frontmatter);
    Cache::clearCache('cache-only');
}

function SetDefaultTemplate() {
    if (empty($_POST["defaultTemplate"])) return;
    $path = "./user/pages/auth/plan2/templates/blank.md";
    $frontmatter = $this->get_frontmatter_as_array($path);

    $frontmatter["defaultTemplate"] = $_POST["defaultTemplate"];

    $this->save_page_with_edited_frontmatter($path, $frontmatter);
    Cache::clearCache('cache-only');
}


function TemplateNameExist($name, $templates) {
    foreach($templates as $template => $_) {
        if ($template == $name) {
            return true;
        }
    }
    return false;
}

function CreatePlanTemplate() {
    if (empty($_POST["templateName"])) return;
    $template_name = $this->trim_all($_POST["templateName"]);

    $path = "./user/pages/auth/plan2/templates/blank.md";
    $frontmatter = $this->get_frontmatter_as_array($path);

    $counter = 0;
    $new_template_name = $template_name;
    while ($this->TemplateNameExist($new_template_name, $frontmatter["templates"])) {
        $counter++;
        $new_template_name = $template_name . "(". $counter .")";
    }

    $frontmatter["templates"][$new_template_name] = array();

   $this->save_page_with_edited_frontmatter($path, $frontmatter);
   Cache::clearCache('cache-only');
   header('Content-type:application/json;charset=utf-8');
   echo json_encode($new_template_name);
}

    // ulozit plan
    function SavePlan(){

        $data = "---" . PHP_EOL .
                "process:". PHP_EOL .
                "    twig: true" . PHP_EOL .
                "    markdown: false" . PHP_EOL .
                "access:" . PHP_EOL .
                "    site:" . PHP_EOL .
                "        plan: true" . PHP_EOL .
                "planTemplate: " . $_POST["template"] . PHP_EOL .
                "plan:" . PHP_EOL;
        if(isset($_POST['data'])){
            $data = PhpTwigExtension::add_season_to_string("data",$data);
        }
        $data .= "---" . PHP_EOL;
        $data .= PhpTwigExtension::parse_file_content_only($_POST["filePath"]);

        PhpTwigExtension::file_force_contents($_POST["filePath"], $data);
        Cache::clearCache('cache-only');
    
    }

    function get_plan_from_template($template){
        if($template == "None"){
            return;
        }

        $templates_path = "./user/pages/auth/plan-templates/default--plan-header.cs.md";
        $frontmatter = PhpTwigExtension::get_frontmatter_as_array($templates_path);

        // retun plan as array
        return $frontmatter[$template]; 
    }

    // nacist sablonu
    function SavePlanTemplate(){

            $page_path = $_POST["filePath"];
            $templates_path = str_replace(array('/plan/', '/plan-next/'), '/plan-templates/', $page_path);
            // get last used teamplates
            $template = $_POST["template"];

            // alternate frontmatter
            $frontmatter = PhpTwigExtension::get_frontmatter_as_array($page_path);             
            $frontmatter['planTemplate'] = $template;                               // set last used template to the chosen one
            $frontmatter['plan'] = PhpTwigExtension::get_plan_from_template($template);        // get chosen plan from page plan-templates
            $frontmatter = Yaml::dump($frontmatter, 10);                            // make string from array 

            // get page content
            $content = PhpTwigExtension::parse_file_content_only($page_path);

            // build page
            $page = PhpTwigExtension::combine_frontmatter_with_content($frontmatter, $content);
           
            // save page
            PhpTwigExtension::file_force_contents($page_path, $page);
            Cache::clearCache('cache-only');

    }




//******************************************************************************************************/
    
    function create_event_id($template, $title, $start){
        $hashStr = $template.$title.$start;
        $date = date_create($start);
        return date_format($date, "Ymd") ."-". hash('crc32', $hashStr);
    }

    /*
     * Convert DMS (degrees / minutes / seconds) to decimal degrees
     *
     * https://github.com/prairiewest/PHPconvertDMSToDecimal
     * 
     * Todd Trann
     * May 22, 2015
     */
    function convertDMSToDecimal($latlng) {
        $valid = false;
        $decimal_degrees = 0;
        $degrees = 0; $minutes = 0; $seconds = 0; $direction = 1;
    
        // Determine if there are extra periods in the input string
        $num_periods = substr_count($latlng, '.');
        if ($num_periods > 1) {
            $temp = preg_replace('/\./', ' ', $latlng, $num_periods - 1); // replace all but last period with delimiter
            $temp = trim(preg_replace('/[a-zA-Z]/','',$temp)); // when counting chunks we only want numbers
            $chunk_count = count(explode(" ",$temp));
            if ($chunk_count > 2) {
                $latlng = preg_replace('/\./', ' ', $latlng, $num_periods - 1); // remove last period
            } else {
                $latlng = str_replace("."," ",$latlng); // remove all periods, not enough chunks left by keeping last one
            }
        }
        
        // Remove unneeded characters
        $latlng = trim($latlng);
        $latlng = str_replace("º"," ",$latlng);
        $latlng = str_replace('°'," ",$latlng);
        $latlng = str_replace("'"," ",$latlng);
        $latlng = str_replace("\""," ",$latlng);
        $latlng = str_replace("  "," ",$latlng);
        $latlng = substr($latlng,0,1) . str_replace('-', ' ', substr($latlng,1)); // remove all but first dash
    
        if ($latlng != "") {
            // DMS with the direction at the start of the string
            if (preg_match("/^([nsewNSEW]?)\s*(\d{1,3})\s+(\d{1,3})\s+(\d+\.?\d*)$/",$latlng,$matches)) {
                $valid = true;
                $degrees = intval($matches[2]);
                $minutes = intval($matches[3]);
                $seconds = floatval($matches[4]);
                if (strtoupper($matches[1]) == "S" || strtoupper($matches[1]) == "W")
                    $direction = -1;
            }
            // DMS with the direction at the end of the string
            elseif (preg_match("/^(-?\d{1,3})\s+(\d{1,3})\s+(\d+(?:\.\d+)?)\s*([nsewNSEW]?)$/",$latlng,$matches)) {
                $valid = true;
                $degrees = intval($matches[1]);
                $minutes = intval($matches[2]);
                $seconds = floatval($matches[3]);
                if (strtoupper($matches[4]) == "S" || strtoupper($matches[4]) == "W" || $degrees < 0) {
                    $direction = -1;
                    $degrees = abs($degrees);
                }
            }
            if ($valid) {
                // A match was found, do the calculation
                $decimal_degrees = ($degrees + ($minutes / 60) + ($seconds / 3600)) * $direction;
            } else {
                // Decimal degrees with a direction at the start of the string
                if (preg_match("/^([nsewNSEW]?)\s*(\d+(?:\.\d+)?)$/",$latlng,$matches)) {
                    $valid = true;
                    if (strtoupper($matches[1]) == "S" || strtoupper($matches[1]) == "W")
                        $direction = -1;
                    $decimal_degrees = $matches[2] * $direction;
                }
                // Decimal degrees with a direction at the end of the string
                elseif (preg_match("/^(-?\d+(?:\.\d+)?)\s*([nsewNSEW]?)$/",$latlng,$matches)) {
                    $valid = true;
                    if (strtoupper($matches[2]) == "S" || strtoupper($matches[2]) == "W" || $degrees < 0) {
                        $direction = -1;
                        $degrees = abs($degrees);
                    }
                    $decimal_degrees = $matches[1] * $direction;
                }
            }
        }
        if ($valid) {
            return substr($decimal_degrees,0,8); // 5 decimal places
        } else {
            return false;
        }
    }

    function normalize_GPS($latlng){
        if(!strpos($latlng, ",")){
            return False;
        }

        $arr = explode( "," , $latlng );

        $lat = $arr[0]; // Latitude of Brno: 49.195060
        $lng = $arr[1]; // Longitude of Brno: 16.606837

        $lat = PhpTwigExtension::convertDMSToDecimal($lat);
        $lng = PhpTwigExtension::convertDMSToDecimal($lng);

        if($lat && $lng){
            return ($lat . ", " . $lng);
        }
        return False;
    }

    //nahravani programu z CSV souboru
    function parse_uploaded_csv(){
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if ($extension != "csv"){ //pokud soubor neni csv vrati error
            PhpTwigExtension::return_ERROR("Nahraný soubor musí být formátu CSV.");
        }
        if (($handle = fopen($_FILES['file']['tmp_name'], "r")) === FALSE) {
            PhpTwigExtension::return_ERROR("Nepodařilo se otevřít soubor");
        }

        //= zahlavi tabulky csv souboru
        $csv_scheme = ["type", "start", "end", "title", "place", "gps", "meetTime", "meetPlace", "transport", "leader", "note", "zabicky", "pulci1", "pulci2", "zaci1", "zaci2", "dorost", "accomodation", "food", "startTime", "eventTypeDescription", "map", "terrain", "return", "price", "program", "thingsToTake", "signups", "doWeOrganize"];
        $approved = ["Z", "M", "T", "S", "BZL", "BBP", "TABOR", "L", "J"]; //ignoruje poznamky

        $num = 0;
        while (($event = fgetcsv($handle)) !== FALSE) {
            if(in_array(trim($event[0]), $approved)){ //parsuje jen spravne zaznamy
                foreach($csv_scheme as $att_index => $attribute){ //prochazi sloupce a uklada do array
                    $event_list[$num][$attribute] = array_key_exists($att_index, $event) ? $event[$att_index] : "";
                }
            }
            $num += 1;
        }

        fclose($handle);
        return $event_list;
    }

    public static function importRacesFromMembers() {
        $body = Response::get("https://members.eob.cz/zbm/api_racelist.php");
        $data = json_decode($body, true);
        if(!array_key_exists("Status", $data) || !array_key_exists("Data", $data) || $data["Status"] != "OK") {
            //mail("otakar.hirs@egmail.com","JSON error zbmob.cz", "Nacteni dat z clenske sekce pres JSON neskoncilo OK, mel bys to asi zkontrolovat. \n Ota - 2018");
            PhpTwigExtension::return_ERROR("Cannot fetch API data from members");
        }
        $time = time();
        $num = 0;
        foreach( $data["Data"] as $id => $event) {
            if (!in_array($event["Type"], ["Z", "S", "T", "V"])) // zavod, soustredeni, trenink, vysetreni
                continue;
            $event_id = date_format(date_create($event["Date1"]), "Y") . "-" . $id;
            if ($event["Cancelled"] == "1") {
                $year = substr($event["Date1"], 0, 4);
                $path = "./user/pages/data/events/". $year ."/". $event_id;
                if (file_exists($path)) {
                    $trashpath = "./user/pages/data/trashbin/events/" . $event_id ;
                    PhpTwigExtension::rcopy($path, $trashpath);
                    PhpTwigExtension::rrmdir($path);
                }
                continue;
            }
            $event_list[$num]["id"] = $event_id;
            $event_list[$num]["start"] = $event["Date1"];
            $event_list[$num]["end"] = array_key_exists("Date2", $event) ? $event["Date2"] : $event["Date1"];
            $event_list[$num]["title"] = $event["Name"];
            //$event_list[$num][""] = $event["Club"];
            $link = $event["Link"];
            if (!empty($link) && !PhpTwigExtension::startsWith($link, "http")) {
                $link = "http://" . $link;
            }
            $event_list[$num]["link"] = $link;
            if(PhpTwigExtension::startsWith($link, "https://oris.orientacnisporty.cz/Zavod?id=" )) {
                $event_list[$num]["orisid"] = substr($link, strlen("https://oris.orientacnisporty.cz/Zavod?id="));
            }
            $event_list[$num]["place"] = $event["Place"];
            $event_list[$num]["type"] = $event["Type"];
            //$event_list[$num][""] = $event["Sport"];
            $rank = (int)$event["Rankings"];
            /* 
            "1": "Celostátní",
            "2": "Morava",
            "4": "Čechy",
            "8": "Oblastní",
            "16": "Mistrovství",
            "32": "Štafety",
            "128": "Veřejný"         
            */

            if ($rank & 1 || $rank & 2 || $rank & 8 || $rank & 16 || $rank & 32) {
                $event_list[$num]["dorost"] = "1";
            }

            if ($rank & 2 || $rank & 8 || $rank & 32) {
                $event_list[$num]["zaci2"] = "1";
                $event_list[$num]["zaci1"] = "1";
            }

            if ($rank & 8) {
                $event_list[$num]["pulci2"] = "1";
                $event_list[$num]["pulci1"] = "1";
                $event_list[$num]["zabicky"] = "1";
            }

            //$event_list[$num][""] = $event["Rank21"];
            $event_list[$num]["note"] = $event["Note"];
            if (array_key_exists("Transport", $event) && strpos($event["Transport"], "Ano") !== false) {   
                $event_list[$num]["transport"] = "společná doprava";
            }
            if (array_key_exists("Accomodation", $event) && strpos($event["Accomodation"], "Ano") !== false) {   
                $event_list[$num]["accomodation"] = "společné ubytování";
            }
            $num++;
        }
        //print_r($event_list);
        PhpTwigExtension::phpUploadProgram($event_list, "members");
    }

    // nahrat program
    public static function phpUploadProgram($event_list=null, $type="csv"){
        if(!$event_list) {
            $event_list = PhpTwigExtension::parse_uploaded_csv();
        }
        
        $groups = ["zabicky", "pulci1", "pulci2", "zaci1", "zaci2", "dorost"];
        $time = time();
        
        foreach($event_list as $event){

            $event['template'] = PhpTwigExtension::get_event_template($event["type"]);
            $event['date'] = date("Y-m-d");
            $event['start'] = PhpTwigExtension::format_date($event['start']);
            $event['end'] = PhpTwigExtension::format_date($event['end']);
            $event['id'] = $event['id'] ? $event['id'] : PhpTwigExtension::create_event_id($event['template'], $event['title'], $event['start']);
            $year = substr($event["start"], 0, 4);

            $path = "./user/pages/data/events/". $year ."/". $event["id"] ."/event.cs.md";

            $changed = true;
            // if file exist, load data
            if(file_exists($path)){
                $frontmatter = PhpTwigExtension::get_frontmatter_as_array($path);
                $changed = false;
            }

            // init taxonomy array if does not exist
            if(!isset($frontmatter['taxonomy']['skupina'])){
                $frontmatter['taxonomy']['skupina'] = array();
            }
                 
            foreach($event as $key => $attribute){
                // add group to taxonomy
                if(in_array($key, $groups) && $attribute == "1"){
                    if(!in_array($key, $frontmatter['taxonomy']['skupina'])){
                        $frontmatter['taxonomy']['skupina'][] = $key;
                        $changed = true;
                    }
                    continue;
                }
                // if no info set, overwrite from given file
                if(empty($frontmatter[$key]) && $attribute){
                    $frontmatter[$key] = $attribute;
                    $changed = true;
                }
            }
            
            if(!empty($event["gps"])){
                $gps = PhpTwigExtension::normalize_GPS($event["gps"]);
                if($gps){
                    $frontmatter["gps"] = $gps;
                    $changed = true;
                }
            }

            if($changed) {
                $frontmatter["import"]["type"] = $type;
                $frontmatter["import"]["time"] = $time;
            }
            $content = PhpTwigExtension::generate_content($frontmatter);
            $page = PhpTwigExtension::combine_frontmatter_with_content(Yaml::dump($frontmatter, 10), $content);

            PhpTwigExtension::file_force_contents($path, $page); 
            unset($frontmatter);
        }
        Cache::clearCache('cache-only');
        
    }


    public function phpSaveEditedEvent($user){
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["POST_type"])){
            if( $_POST["POST_type"] == "editEvent" ){
                // kontrola doručení potřebných údajů
                if(empty($_POST["title"])){
                    PhpTwigExtension::return_ERROR('Není vyplněn "Název"');
                }
                if(empty($_POST["start"])){
                    PhpTwigExtension::return_ERROR('Není vyplněno "Datum"');
                }
                if(empty($_POST["place"])){
                    PhpTwigExtension::return_ERROR('Není vyplněno "Místo"');
                }
                if(isset($_POST["delete"])){
                    $path = "./user/pages/data/events/". substr($_POST["id"], 0 , 4) ."/". $_POST["id"] ;
                    $trashpath = "./user/pages/data/trashbin/events/" . $_POST["id"];
                    PhpTwigExtension::rcopy($path, $trashpath);
                    PhpTwigExtension::rrmdir($path);
                    PhpTwigExtension::log_grav($user . " | EVENT removed | " . $_POST["id"]);
                    Cache::clearCache('cache-only');
                    die(); 
                }
                
                
                $data = ["type", "title", "start", "end",  "place", "meetTime", "meetPlace", "link", "eventTypeDescription", "startTime", "map", "terrain", "transport", "accomodation", "food", "leader", "doWeOrganize", "note", "return", "price", "program", "thingsToTake", "signups"];
                $group_arr = ["zabicky", "pulci1", "pulci2", "zaci1", "zaci2", "dorost"];
                
                
                $id = empty($_POST["id"]) ? PhpTwigExtension::create_event_id($_POST['type'], $_POST['title'], $_POST['start']) : $_POST["id"];
                $year = substr($id, 0 , 4);
                $path = "./user/pages/data/events/". $year ."/". $id ."/event.cs.md";

                $new = file_exists($path) ? false : true;

                if ($new) {
                    $frontmatter["id"] = $id;
                }
                else {              
                    $frontmatter = PhpTwigExtension::get_frontmatter_as_array($path);   //rozparsuje existujici soubor  
                }
                $frontmatter['template'] = PhpTwigExtension::get_event_template($_POST['type']);

                foreach($data as $attribute){
                    if(isset($_POST[$attribute])){
                        $frontmatter[$attribute] = trim($_POST[$attribute]);
                    }
                }

                if(empty($frontmatter['end'])){
                    $frontmatter['end'] = $_POST["start"];
                }

                // groups
                foreach($group_arr as $key => $group){
                    if($_POST[$group]){
                        if (!isset($frontmatter['taxonomy']['skupina']) || !in_array($group, $frontmatter['taxonomy']['skupina'])) {
                            $frontmatter['taxonomy']['skupina'][] = $group;
                        }
                    }
                    else{
                        if (isset($frontmatter['taxonomy']['skupina'])){
                            $del = array_search( $group , $frontmatter['taxonomy']['skupina'] );
                            unset($frontmatter['taxonomy']['skupina'][$del]);
                        }
                    }
                  }

                // normalize GPS
                if(!empty($_POST["GPS"])){
                    $gps = PhpTwigExtension::normalize_GPS($_POST["GPS"]);
                    if($gps){
                        $frontmatter["gps"] = $gps;
                    }
                    else{
                        PhpTwigExtension::return_ERROR('<br>Nepodporovaný formát GPS: hodnoty zem. šířky a délky musí být odděleny čárkou. <br>např 50°42\'38.9"N<b>,</b> 15°36\'56.6"E');
                    }
                }

                //routes
                $i = 0;
                unset($frontmatter["routes"]);
                while(isset($_POST["routeName"][$i], $_POST["routeLink"][$i])){
                    if(!empty($_POST["routeName"][$i]) && !empty($_POST["routeLink"][$i])){
                        $frontmatter["routes"][$i]["name"] = $_POST["routeName"][$i];
                        $frontmatter["routes"][$i]["link"] = $_POST["routeLink"][$i];
                    }
                    $i++;
                }

                //result
                $i = 0;
                unset($frontmatter["results"]);
                while(isset($_POST["resultsName"][$i], $_POST["resultsLink"][$i])){
                    if(!empty($_POST["resultsName"][$i]) && !empty($_POST["resultsLink"][$i])){
                        $frontmatter["results"][$i]["name"] = $_POST["resultsName"][$i];
                        $frontmatter["results"][$i]["link"] = $_POST["resultsLink"][$i];
                    }
                    $i++;
                }
                
                //combine
                if ($new) {
                    $content = PhpTwigExtension::generate_content($frontmatter);
                }
                else {
                    $content = PhpTwigExtension::parse_file_content_only($path);
                }
                
                $frontmatter = Yaml::dump($frontmatter, 10);
                $page = PhpTwigExtension::combine_frontmatter_with_content($frontmatter, $content);
                //print_r($_POST);
                //print_r($frontmatter);
                PhpTwigExtension::file_force_contents($path, $page);
                if ($new) {
                    PhpTwigExtension::log_grav($user . " | EVENT created | " . $id);
                    $result = array("id" => $id);
                    echo json_encode($result);

                }
                else {
                    PhpTwigExtension::log_grav($user . " | EVENT edited | " . $id);
                }
                Cache::clearCache('cache-only');
            }
        }
      }
    }
//******** POLARIS *********
    function make_jpeg_thumbnail($source, $target){
        $source = realpath($source);
        $im = new \Imagick();
        $im->setResolution(595, 842);    // set loaded resolution
        $im->readImage($source."[0]");    // 0-first page, 1-second page
        $im->transformimagecolorspace(\Imagick::COLORSPACE_SRGB);     //CMYK to RGB
        $im->setImageBackgroundColor('#ffffff');                      //prevents black background
        $im = $im->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);   //merge layers
        $im->setimageformat("jpeg");                                  //converts to JPEG
        $im->setCompression(\Imagick::COMPRESSION_JPEG);
        $im->setCompressionQuality(80);
        $im->resizeImage(595, 842, \Imagick::FILTER_LANCZOS,1);       //set saved resolution
        $im->writeImage($target);                                     //save
        $im->clear();
        $im->destroy();
    }

    function save_PDF($savePath, $fileTitle, $makeThumbnail=True){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['PDF']['tmp_name']);
        if ($mime != 'application/pdf') {
            PhpTwigExtension::return_ERROR('Nahraný soubor není PDF!');
        }
        if(!is_dir($savePath)){
            mkdir($savePath);
        }
        $saveFilesPath = $savePath ."/". $fileTitle;
        move_uploaded_file($file_tmp=$_FILES["PDF"]["tmp_name"], $saveFilesPath);

        if($makeThumbnail){
            PhpTwigExtension::make_jpeg_thumbnail($saveFilesPath, $saveFilesPath . ".jpg");
        }
    }

    public function SavePolaris(){ 

        if(empty($_FILES["PDF"]["tmp_name"])){
            PhpTwigExtension::return_ERROR("Nahrání PDF souboru se nezdařilo.");
        }

        // init vars
        $pagePath = './user/pages/data/polaris/blank.md';
        $savePath = './user/pages/data/polaris/' . $_POST['year'];
        $polarisYear = $_POST['year'];
        $polarisNumber = "p" . $_POST['cislo'];
        $fileTitle = "Polaris_" . $_POST['year'] . "_" . $_POST['cislo'] . ".pdf" ;

        //get frontmatter
        $frontmatter = PhpTwigExtension::get_frontmatter_as_array($pagePath);

        // add polaris to frontmatter
        if(isset($frontmatter['polaris'][$polarisYear]) && in_array($fileTitle, $frontmatter['polaris'][$polarisYear])){
            PhpTwigExtension::return_ERROR("Už je nahrané stejné číslo Polarisu.");
        }

        $frontmatter['polaris'][$polarisYear][$polarisNumber] = $fileTitle;
        krsort($frontmatter['polaris']);
        krsort($frontmatter['polaris'][$polarisYear]);

        // save pdf and jpeg thumbnail
        PhpTwigExtension::save_PDF($savePath, $fileTitle);
        
        // build page
        $pageFrontmatter = Yaml::dump($frontmatter, 10);
        $pagecontent = PhpTwigExtension::parse_file_content_only($pagePath);
        $page = PhpTwigExtension::combine_frontmatter_with_content($pageFrontmatter, $pagecontent);

        // save page to file
        file_put_contents($pagePath, $page);
        Cache::clearCache('cache-only');
    }

    public function DeletePolaris(){
    
        // init vars
        $polarisYear = $_POST['year'];
        $polarisNumber = "p" . $_POST['cislo'];
        $pagePath = './user/pages/data/polaris/blank.md';
        $filePath = './user/pages/data/polaris/' . $_POST['year']. "/" . $_POST['pdf'];
        
        // get frontmatter
        $frontmatter = PhpTwigExtension::get_frontmatter_as_array($pagePath);

        // remove polaris from frontmatter
        unset($frontmatter['polaris'][$polarisYear][$polarisNumber]);

        // delete pdf and jpeg thumbnail
        if(file_exists($filePath)){
            unlink($filePath);
        }
        if(file_exists($filePath .".jpg")){
            unlink($filePath .".jpg");
        }
        // remove years if year is empty
        $frontmatter['polaris'] = array_filter($frontmatter['polaris']);

        // build page
        $pageFrontmatter = Yaml::dump($frontmatter, 10);
        $pagecontent = PhpTwigExtension::parse_file_content_only($pagePath);
        $page = PhpTwigExtension::combine_frontmatter_with_content($pageFrontmatter, $pagecontent);

        // save page to file
        file_put_contents($pagePath, $page);
        Cache::clearCache('cache-only');
    }
//******** Mapova Teorie *********
    public function SaveMapT(){ 

        // init vars
        $maptGroup = $_POST['group'];
        $fileTitle = $_POST['date'] . ".pdf" ;
        $pagePath = './user/pages/data/maptheory/blank.md';
        $savePath = './user/pages/data/maptheory/' . $maptGroup;

        //get frontmatter
        $frontmatter = PhpTwigExtension::get_frontmatter_as_array($pagePath);

        // add maptheory to frontmatter
        if(isset($frontmatter['maptheory'][$maptGroup]) && in_array ( $fileTitle , $frontmatter['maptheory'][$maptGroup] ) ){
            PhpTwigExtension::return_ERROR("Už je nahrana mapova teorie se stejnym datem a skupinou");
        }
        else{
            $frontmatter['maptheory'][$maptGroup][] = $fileTitle;
            arsort($frontmatter['maptheory'][$maptGroup]);
        } 

        // save pdf and jpeg thumbnail
        PhpTwigExtension::save_PDF($savePath, $fileTitle, $makeThumbnail=False);
        
        // build page
        $pageFrontmatter = Yaml::dump($frontmatter, 10);
        $pagecontent = PhpTwigExtension::parse_file_content_only($pagePath);
        $page = PhpTwigExtension::combine_frontmatter_with_content($pageFrontmatter, $pagecontent);

        // save page to file
        file_put_contents($pagePath, $page);
        Cache::clearCache('cache-only');
    }

    public function DeleteMapT(){

        // init vars
        $maptName = $_POST['name'];
        $maptGroup = $_POST['group'];
        $pagePath = './user/pages/data/maptheory/blank.md';
        $filePath = './user/pages/data/maptheory/' . $maptGroup . '/' . $maptName;
        
        // get frontmatter
        $frontmatter = PhpTwigExtension::get_frontmatter_as_array($pagePath);
        var_dump($frontmatter);

        // remove maptheory from frontmatter
        if (($key = array_search($maptName, $frontmatter['maptheory'][$maptGroup])) !== false) {
            unset($frontmatter['maptheory'][$maptGroup][$key]);
        }
        
        // delete pdf
        if(file_exists($filePath)){
            unlink($filePath);
        }

        // build page
        $pageFrontmatter = Yaml::dump($frontmatter, 10);
        $pagecontent = PhpTwigExtension::parse_file_content_only($pagePath);
        $page = PhpTwigExtension::combine_frontmatter_with_content($pageFrontmatter, $pagecontent);

        // save page to file
        file_put_contents($pagePath, $page);
        Cache::clearCache('cache-only');
    }
    
    public function collectionToEventsByDate($collection){
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
