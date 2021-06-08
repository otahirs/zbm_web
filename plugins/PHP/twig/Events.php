<?php
namespace Grav\Plugin;
use Grav\Common\Cache;
use Grav\Plugin\Utils;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Filesystem\Folder;
use Grav\Common\GPM\Response;

require_once(__DIR__ . "/Utils.php");

class Events extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'Events';
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('phpUploadProgram', [$this, 'ImportEvents']),
            new \Twig_SimpleFunction('phpEditBliziSe', [$this, 'editBliziSeFunction']),
            new \Twig_SimpleFunction('phpSaveEditedEvent', [$this, 'phpSaveEditedEvent']),
        ];
    }   

    


    //******************************************************************************************************/
    //updatuje kontent zobrazovany v Blizi se
    public static function editBliziSeFunction(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        $page_url = "/data/events/". substr($_POST["id"], 0, 4) ."/". $_POST["id"];
        $page = Grav::instance()['page']->find($page_url);
        
        if(isset($_POST["regenerate"]) && $_POST["regenerate"]){
            $content = self::generate_content((array)$page->header());
            $page->content($content);
        }
        else{
            $page->content($_POST["content"]);
        }
        $page->save();
        Cache::clearCache('cache-only');
    }

    
    
    

    //nahravani programu z CSV souboru
    static function parse_uploaded_csv(){
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if ($extension != "csv"){ //pokud soubor neni csv vrati error
            Utils::return_ERROR("Nahraný soubor musí být formátu CSV.");
        }
        if (($handle = fopen($_FILES['file']['tmp_name'], "r")) === FALSE) {
            Utils::return_ERROR("Nepodařilo se otevřít soubor");
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
        Grav::instance()['twig']->init(); // fix function crash if runned from scheduler
        $body = Response::get("https://members.eob.cz/zbm/api_racelist.php");
        $data = json_decode($body, true);
        if(!array_key_exists("Status", $data) || !array_key_exists("Data", $data) || $data["Status"] != "OK") {
            //mail("otakar.hirs@egmail.com","JSON error zbmob.cz", "Nacteni dat z clenske sekce pres JSON neskoncilo OK, mel bys to asi zkontrolovat. \n Ota - 2018");
            Utils::return_ERROR("Cannot fetch API data from members");
        }
        $num = 0;
        foreach( $data["Data"] as $id => $event) {
            if (!in_array($event["Type"], ["Z", "S", "T", "V"])) // zavod, soustredeni, trenink, vysetreni
                continue;
            $event_id = date_format(date_create($event["Date1"]), "Y") . "-" . strtolower($id);
            if ($event["Cancelled"] == "1") {
                self::trashEvent($event_id);
                continue;
            }
            $event_list[$num]["id"] = $event_id;
            $event_list[$num]["start"] = $event["Date1"];
            $event_list[$num]["end"] = array_key_exists("Date2", $event) ? $event["Date2"] : $event["Date1"];
            $event_list[$num]["title"] = $event["Name"];
            //$event_list[$num][""] = $event["Club"];
            $link = $event["Link"];
            if (!empty($link) && !Utils::startsWith($link, "http")) {
                $link = "https://" . $link;
            }
            $event_list[$num]["link"] = $link;
            if(Utils::startsWith($link, "https://oris.orientacnisporty.cz/Zavod?id=" )) {
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
                $event_list[$num]["hobby"] = "1";
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
        self::ImportEvents($event_list, "members");
    }

    // nahrat program
    public static function ImportEvents($event_list=null, $type="csv"){
        if(!$event_list) {
            $event_list = self::parse_uploaded_csv();
        }
        
        foreach($event_list as $event){

            $event['template'] = self::get_event_template($event["type"]);
            $event['date'] = date("Y-m-d");
            $event['start'] = Utils::format_date($event['start']);
            $event['end'] = Utils::format_date($event['end']);
            $event['id'] = $event['id'] ? $event['id'] : self::create_event_id($event['template'], $event['title'], $event['start']);

            $page_url = "/data/events/". substr($event["id"], 0, 4) ."/". $event["id"];

            $changed = false;

            $pages = Grav::instance()['pages'];
            $pages->init();
            $page = $pages->find($page_url);
            if ($page == null) {
                $page = new Page();
                $page->filePath("./user/pages{$page_url}/event.md");
                $changed = true;
            }
            else {
                $frontmatter = (array)$page->header();
            }

            // init taxonomy array if does not exist
            if(!isset($frontmatter['taxonomy']['skupina'])){
                $frontmatter['taxonomy']['skupina'] = array();
            }
                 
            foreach($event as $key => $attribute){
                // add group to taxonomy
                if(in_array($key, ["zabicky", "pulci1", "pulci2", "zaci1", "zaci2", "dorost", "hobby"]) && $attribute == "1"){
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
                $gps = self::normalize_GPS($event["gps"]);
                if($gps){
                    $frontmatter["gps"] = $gps;
                    $changed = true;
                }
            }

            if($changed) {
                if(!$page->exists()) {
                    $frontmatter["import"]["type"] = $type;
                    $frontmatter["import"]["time"] = time();
                    $content = self::generate_content($frontmatter);
                    $page->content($content);
                    Utils::log("event imported | from {$type} | {$event['id']}");
                }
                else {
                    Utils::log("event edited | from {$type} | {$event['id']}");
                }
                $page->header($frontmatter);
                $page->save();
            }
            unset($frontmatter);
        }
        Cache::clearCache('cache-only');
        
    }    

    public static function phpSaveEditedEvent(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        
        // kontrola doručení potřebných údajů
        if(empty($_POST["title"])){
            Utils::return_ERROR('Není vyplněn "Název"');
        }
        if(empty($_POST["start"])){
            Utils::return_ERROR('Není vyplněno "Datum"');
        }
        if(empty($_POST["place"])){
            Utils::return_ERROR('Není vyplněno "Místo"');
        }
        if(isset($_POST["delete"])){
            if(empty($_POST["id"])) return;
            self::trashEvent($_POST["id"]);
            Cache::clearCache('cache-only');
            die(); 
        }

        $data = ["type", "title", "start", "end",  "place", "meetTime", "meetPlace", "link", "eventTypeDescription", "startTime", "map", "terrain", "transport", "accomodation", "food", "leader", "doWeOrganize", "note", "return", "price", "program", "thingsToTake", "signups"];
        $group_arr = ["zabicky", "pulci1", "pulci2", "zaci1", "zaci2", "dorost", "hobby"];

        $id = empty($_POST["id"]) ? self::create_event_id($_POST['type'], $_POST['title'], $_POST['start']) : $_POST["id"];
        $year = substr($id, 0 , 4);
        $path = "./user/pages/data/events/". $year ."/". $id ."/event.md";

        $page_url = "/data/events/". $year ."/". $id;
        $page = Grav::instance()['page']->find($page_url);
        if ($page == null) {
            $page = new Page();
            $page->filePath("./user/pages{$page_url}/event.md");
            $frontmatter["id"] = $id;
            $new = true;
        }
        else {
            $frontmatter = (array)$page->header();
            $new = false;
        }

        $frontmatter['template'] = self::get_event_template($_POST['type']);

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
            $gps = self::normalize_GPS($_POST["GPS"]);
            if($gps){
                $frontmatter["gps"] = $gps;
            }
            else{
                Utils::return_ERROR('<br>Nepodporovaný formát GPS: hodnoty zem. šířky a délky musí být odděleny čárkou. <br>např 50°42\'38.9"N<b>,</b> 15°36\'56.6"E');
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

        if($new) {
            $content = self::generate_content($frontmatter);
            $page->content($content);
        }

        $page->header($frontmatter);
        $page->save();      

        if ($new) {
            Utils::log("EVENT | created | " . $id);
            $result = array("id" => $id);
            echo json_encode($result);

        }
        else {
            Utils::log("EVENT | edited | " . $id);
        }

        Cache::clearCache('cache-only');
    }

    /*****************************************************************************************************
     * helpers
    */
    static function trashEvent($id) {
        if(empty($id)) return;
        $year = substr($id, 0 , 4);
        $oldpath = "./user/pages/data/events/". $year ."/". $id ;
        if(!is_dir($oldpath)) return;
        $newpath = "./user/pages/data/trashbin/events/". $year ."/". $id ;
        if (!is_dir(dirname($newpath))) {
            mkdir(dirname($newpath), 0775, true);
        }
        Folder::delete($newpath);
	    rename($oldpath, $newpath);
        Utils::log("EVENT | removed | " . $id);
    }

    static function create_event_id($template, $title, $start){
        $hashStr = $template.$title.$start;
        $date = date_create($start);
        return date_format($date, "Ymd") ."-". hash('crc32', $hashStr);
    }
    
    static function get_event_template($event){
        switch ($event) {
            case "Z":
            case "BZL":
              return "zavod";
            case "M":
            case "T":
            case "BBP":
              return "trenink";
            case "S":
            case "TABOR":
              return "soustredeni";
            //case "TABOR":
            // $template = "tabor";    break;
            default:
              return "akce";
         }
    }

    static function generate_content($race){
        $content = "";
        if(!empty($race["doWeOrganize"]) && $race["doWeOrganize"]=="1" ) {
            $content .= "**Pořádáme!!**" . PHP_EOL;
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

    static function normalize_GPS($latlng){
        if(!strpos($latlng, ",")){
            return False;
        }

        $arr = explode( "," , $latlng );

        $lat = $arr[0]; // Latitude of Brno: 49.195060
        $lng = $arr[1]; // Longitude of Brno: 16.606837

        $lat = self::convertDMSToDecimal($lat);
        $lng = self::convertDMSToDecimal($lng);

        if($lat && $lng){
            return ($lat . ", " . $lng);
        }
        return False;
    }

    /*
     * Convert DMS (degrees / minutes / seconds) to decimal degrees
     *
     * https://github.com/prairiewest/PHPconvertDMSToDecimal
     * 
     * Todd Trann
     * May 22, 2015
     */
    static function convertDMSToDecimal($latlng) {
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

    

    
}
?>
