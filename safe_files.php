<?php
/**
 * Created by PhpStorm.
 * User: abderrahimelimame
 * Date: 9/21/16
 * Time: 23:19
 */


// include the database connection class
include 'config/DataBase.php';
// include the config file
include 'config/Config.php';
// include the Helper class
include 'application/helpers/Helper.php';
// include the ImageResize class
include 'application/helpers/ImageResize.php';


$_DB = new DataBase($_Config);
$_DB->connect();
$_DB->selectDB();
$_GB = new Helper($_DB);
use Eventviva\ImageResize;

if (isset($_GET['hash'])) {

    if (isset($_GET['images'])) {
        $path = $_GB->getSafeImage($_GET['hash']);
        if ($path != null) {
            $image = new ImageResize($path);
            if (isset($_GET['profile'])) {
                $image->crop(500, 500);
                $image->output(IMAGETYPE_PNG, 9);
            } else if (isset($_GET['profilePreview'])) {
                $image->crop(300, 300);
                $image->output(IMAGETYPE_PNG, 9);
            } elseif (isset($_GET['profilePreviewHolder'])) {
                $image->crop(30, 30);
                $image->output(IMAGETYPE_PNG, 9);
            } else if (isset($_GET['rowImage'])) {
                $image->crop(70, 70);
                $image->output(IMAGETYPE_PNG, 9);
            } else if (isset($_GET['settings'])) {
                $image->crop(100, 100);
                $image->output(IMAGETYPE_PNG, 9);
            } else if (isset($_GET['editProfile'])) {
                if (file_exists($path)) {
                    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                    header("Cache-Control: public"); // needed for i.e.
                    header("Content-Type: application/txt");
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . basename($path) . "\"");
                    header("Content-Length:" . filesize($path));
                    readfile($path);
                    die();
                } else {
                    die("Error: File not found.");
                }
            } else if (isset($_GET['messageImage'])) {
                if (file_exists($path)) {
                    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                    header("Cache-Control: public"); // needed for i.e.
                    header("Content-Type: application/txt");
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . basename($path) . "\"");
                    header("Content-Length:" . filesize($path));
                    readfile($path);
                    die();
                } else {
                    die("Error: File not found.");
                }
            } else if (isset($_GET['messageImageHolder'])) {
                $image->crop(30, 30);
                $image->output(IMAGETYPE_PNG, 9);
            }
        } else {
            ob_clean();
            header('Content-Type: image/jpg');
            echo file_get_contents(null);
        }
    } else if (isset($_GET['videos'])) {
        if (isset($_GET['messageVideo'])) {
            $url = $_GB->getVideoFileUrl($_GET['hash']);
            if (file_exists($url)) {
                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for i.e.
                header("Content-Type: application/txt");
                header("Content-Transfer-Encoding: Binary");
                header("Content-disposition: attachment; filename=\"" . basename($url) . "\"");
                header("Content-Length:" . filesize($url));
                readfile($url);
                die();
            } else {
                die("Error: File not found.");
            }
        } else if (isset($_GET['messageVideoThumbnail'])) {
            $urlThumbnail = $_GB->getVideoThumbnailFileUrl($_GET['hash']);
            $imageThumbnail = new ImageResize($urlThumbnail);
            if ($urlThumbnail != null) {
                $imageThumbnail->crop(500, 500);
                $imageThumbnail->output(IMAGETYPE_PNG, 9);
            } else {
                ob_clean();
                header('Content-Type: image/jpg');
                echo file_get_contents(null);
            }
        }

    } else if (isset($_GET['audios'])) {
        if (isset($_GET['messageAudio'])) {
            $url = $_GB->getAudioFileUrl($_GET['hash']);
            if (file_exists($url)) {

                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for i.e.
                header("Content-Type: application/txt");
                header("Content-Transfer-Encoding: Binary");
                header("Content-disposition: attachment; filename=\"" . basename($url) . "\"");
                header("Content-Length:" . filesize($url));
                readfile($url);
                die();
            } else {
                die("Error: File not found.");
            }
        }
    } else if (isset($_GET['documents'])) {
        if (isset($_GET['messageDocument'])) {
            $url = $_GB->getDocumentFileUrl($_GET['hash']);
            if (file_exists($url)) {

                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for i.e.
                header("Content-Type: application/txt");
                header("Content-Transfer-Encoding: Binary");
                header("Content-disposition: attachment; filename=\"" . basename($url) . "\"");
                header("Content-Length:" . filesize($url));
                readfile($url);
                die();
            } else {
                die("Error: File not found.");
            }
        }
    } else if (isset($_GET['backup'])) {
        if (isset($_GET['messageBackup'])) {
            $url = $_GB->getBackupFileUrl($_GET['hash']);
            if (file_exists($url)) {

                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for i.e.
                header("Content-Type: application/txt");
                header("Content-Transfer-Encoding: Binary");
                header("Content-disposition: attachment; filename=\"" . basename($url) . "\"");
                header("Content-Length:" . filesize($url));
                readfile($url);
                die();
            } else {
                die("Error: File not found.");
            }
        }
    }


}
