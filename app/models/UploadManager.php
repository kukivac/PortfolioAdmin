<?php


namespace app\models;


use RuntimeException;
use Nette\Http\FileUpload as FileUpload;

class UploadManager
{
    /**
     * @param $values
     *
     * @return bool|array
     */
    public static function UploadMultiple($values)
    {
        $files = array();
        $filenames = array();
        try {
            foreach ($values as $file) {
                /**
                 * @var FileUpload $file
                 */
                switch($file->getImageFileExtension()){
                    case"jpeg":
                        $ext = "jpg";
                        break;
                    case "png":
                        $ext = "png";
                        break;
                    case "tiff":
                        $ext = "tiff";
                        break;
                }
                array_push($files, ($filename = sha1_file($file->getTemporaryFile())).".".$ext);
                array_push($filenames,$file->getSanitizedName());
                $fileNameWDir = sprintf(
                    'images/fullView/%s.%s',
                    $filename,
                    $ext
                );

                if (!move_uploaded_file(
                    $file->getTemporaryFile(),
                    $fileNameWDir
                )) {
                    throw new RuntimeException();
                }
                ImageManager::defaultImage($fileNameWDir);
                ImageManager::makeThumbnail($fileNameWDir);
            }
        } catch (RuntimeException $exception) {
            var_dump($exception);
            if(!empty($files)){
                foreach ($files as $filename){
                    unlink("images/fullView/".$filename);
                    unlink("images/thumbnail/".$filename);
                }
            }
            return false;
        }
        return ["filenames"=>$files,"file-names"=>$filenames];
    }
}