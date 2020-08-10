<?php
namespace Grav\Plugin;
use Grav\Common\Cache;
use Grav\Plugin\Utils;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Filesystem\Folder;

require_once(__DIR__ . "/Utils.php");

class News extends \Grav\Common\Twig\TwigExtension
{    
    public function getName()
    {
        return 'News';
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('phpNewsAdd', [$this, 'NewsAdd']),
            new \Twig_SimpleFunction('phpNewsUpdate', [$this, 'NewsUpdate']),
            new \Twig_SimpleFunction('phpNewsDelete', [$this, 'NewsDelete']),
        ];
    }   

    public static function NewsAdd() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        $id = date("Ymd-Hisv");
        $date = date("Y-m-d");
        $user = Grav::instance()['session']->user;
        $author = $user->fullname ?? $user->username;
        $images = !empty($_POST['img']) ? $_POST['img'] : array();
        self::news_to_file($id, $date, $_POST["title"], $_POST["content"], $images, $author);
        if (!empty($_FILES)) {
            self::process_files($id, 1000, substr($date, 0 , 4));
        }
        Utils::log("NEWS | created | " . $id);
        Cache::clearCache('cache-only');
    }

    public static function NewsUpdate() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        $id = $_POST["id"];
        $date = date("Y-m-d", strtotime(str_replace(' ','', $_POST["date"])));
        $images = !empty($_POST['img']) ? $_POST['img'] : array();
        self::news_to_file($id, $date, $_POST["title"], $_POST["content"], $images, $_POST["author"]);
        if (!empty($_FILES)) {
            self::process_files($id, 1000, substr($date, 0 , 4));
        }
        Utils::log(" | NEWS | edited | " . $id);
        Cache::clearCache('cache-only');
    }

    public static function NewsDelete() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        if(empty($_POST["id"])) return;
        $year = substr($_POST["id"], 0 , 4);
        $oldpath = "./user/pages/data/news/" . $year . "/". $_POST['id'];
        $newpath = "./user/pages/data/trashbin/news/" . $year . "/". $_POST['id'];
        Folder::move($oldpath, $newpath);
        Utils::log("News | removed | " . $_POST["id"]);
        Cache::clearCache('cache-only');
    }


    static function news_to_file($id, $date, $title, $content, $images=array(), $author){
        
        $year = substr($id, 0 , 4);
        
        $news = "---" . PHP_EOL .
                "title: '" . str_replace("'","''",$title) . "'" . PHP_EOL . // escape ' in frontmatter
                "date: '" . $date . "'" . PHP_EOL .
                "template: novinka" . PHP_EOL .
                "id: '" . $id . "'" . PHP_EOL .
                "user: '" . $author . "'" . PHP_EOL .
                "pictures:" . PHP_EOL;

        foreach ($images as $img) {
            if(isset($img['img_delete']) && $img['img_delete'] == "true"){
                $imgPath =     "./user/pages/data/news/" . $year . "/". $id . "/img/" . $img['img_name'];
                $previewPath = "./user/pages/data/news/" . $year . "/". $id . "/img/" . $img['img_name'] . "_preview.jpg";
                if (file_exists($imgPath)) unlink($imgPath);
                if (file_exists($previewPath)) unlink($previewPath);
            }
            else {
                $news .=    "    - name: '" . $img['img_name'] . "'" . PHP_EOL .
                            "      ratio: '". $img['img_ratio'] ."'" . PHP_EOL;
            }
        }

        $news .= "---" . PHP_EOL .
                $content . PHP_EOL;

        $news = htmlspecialchars($news, ENT_NOQUOTES, 'UTF-8');


        $path = "./user/pages/data/news/" . $year . "/". $id . "/";
        if (!file_exists($path)) mkdir($path, 0777, true);
        file_put_contents("{$path}default.md", $news);
    }

    static function process_files($id, $previewWidthInPx, $year){
    
        $storeFolder = "./user/pages/data/news/" . $year . "/". $id . "/img/";

        // check file type
        $extension=array("jpeg","jpg","png","gif","JPEG","JPG","PNG","GIF","jpe","jif","jfif","jfi","JPE","JIF","JFIF","JFI"); //.jpe .jif, .jfif, .jfi jsou soubory jpeg
        foreach($_FILES["file"]["tmp_name"] as $key=>$tmp_name){
            $file_name=$_FILES["file"]["name"][$key];
            $ext=pathinfo($file_name, PATHINFO_EXTENSION);
            if(!in_array($ext,$extension)) {
                Utils::return_ERROR("<em>" . $file_name . "</em>není podporovaný typ obrázku");
            }
        }

        foreach($_FILES["file"]["tmp_name"] as $key=>$tmp_name){

            $file_name=$_FILES["file"]["name"][$key];
            $file_tmp=$_FILES["file"]["tmp_name"][$key];
            
            if(!file_exists($storeFolder . $file_name)){
                if(!is_dir($storeFolder)){
                    mkdir($storeFolder);
                }
                $saveImagePath = $storeFolder . $file_name;
                $savePreviewPath = $saveImagePath . "_preview.jpg";
                move_uploaded_file($file_tmp, $saveImagePath);
                self::createThumbnail($saveImagePath, $savePreviewPath, $previewWidthInPx, $targetHeight = null);
            };
        }
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
    static function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {

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
}
?>
