<?php
namespace Grav\Plugin;
use Grav\Common\Grav;
use Symfony\Component\Yaml\Yaml;

class Utils
{    
    public static function return_ERROR($errMsg){
        http_response_code(500); // 500 - Internal server error
        echo $errMsg;
        die();
    }

    public static function trim_all($str){
        $str = str_replace( "'" , "" , $str );
        $str = str_replace( '"' , "" , $str );
        $str = trim($str);
        $str = htmlspecialchars(html_entity_decode($str, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        $str = str_replace( "\n" , "<br>" , $str );
        return $str;
    }

    public static function startsWith ($string, $startString) { 
        $len = strlen($startString); 
        return (substr($string, 0, $len) === $startString); 
    }
    
    public static function make_jpeg_thumbnail($source, $target){
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

    public static function save_PDF($pdf_save_path, $pdf_file_name, $makeThumbnail=True){
        $file = $_FILES['PDF']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file);
        if ($mime != 'application/pdf') {
            Utils::return_ERROR('Nahraný soubor není PDF!');
        }
        if(!is_dir($pdf_save_path)){
            mkdir($pdf_save_path);
        }
        $saveFilesPath = $pdf_save_path ."/". $pdf_file_name;
        move_uploaded_file($file, $saveFilesPath);

        if($makeThumbnail){
            self::make_jpeg_thumbnail($saveFilesPath, $saveFilesPath . ".jpg");
        }
    }


    public static function log($msg){
        $user = Grav::instance()['session']->user;
        if ($user->authenticated) {
            $username = !empty($user->fullname) ? $user->fullname : $user->username;
        }
        else {
            $username = "System";
        }
        $log_msg = "{$username} | {$msg}";
        Grav::instance()['log']->info($log_msg);
    }

    public static function format_date($date){
        return date("Y-m-d", strtotime(str_replace(' ','', $date))); // d. m. Y => Y-m-d
    }

}
?>
