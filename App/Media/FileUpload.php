<?php

namespace App\Media;

use Exception;

class FileUpload
{
    public static $file;
    const DEFAULT_SIZE = "5000000"; // 5MB //
    const ACCEPTED_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    public static function uploadImage($dir, $file, $encode = TRUE)
    {
        $name = $file['name'];
        $tmpName = $file['tmp_name'];
        $size = $file['size'];
        $explodedExtensions = explode(".", $name);
        $fileExtension = strtolower(end($explodedExtensions));
        if (!in_array($fileExtension, self::ACCEPTED_EXTENSIONS)) {
            throw new Exception("File type '<b>.{$fileExtension}</b>' is not allowed. Allowed: ['jpg','jpeg','png']", 403);
        } else if ($size > self::DEFAULT_SIZE) {
            throw new Exception("Size of file is too large. Accepted: <= 5MB");
        } else {
            if ($encode) {
                $name = md5(uniqid("Mercury")) . "." . $fileExtension;
            }
            $file = $dir . basename($name);
            if (move_uploaded_file($tmpName, $file)) {
                if ($fileExtension == "jpg") {
                    $fileExtension = "jpeg";
                }
                $image = "imagecreatefrom" . $fileExtension;
                $image = $image($file);
                self::$file = $name;
                return imagejpeg($image, $file, 60);
            }
            return FALSE;
        }
    }

    /**
     * @throws Exception
     */
    public static function uploadFile($dir, $file, $encode = TRUE, $allowed = ['pdf', 'odt', 'docx', 'txt', 'xlsx', 'xls', 'html', 'csv'], $size = 5000000)
    {
        $filename = $file['name'];
        $filesize = $file['size'];
        $tmp = $file['tmp_name'];
        $explodedFile = explode(".", $filename);
        $fileExtension = strtolower(end($explodedFile));

        // check if file is a valid format
        if (!in_array($fileExtension, $allowed)) {
            throw new Exception("File format: '<b>.{$fileExtension}</b>' is not allowed");
        } else if ($filesize > $size) {
            throw new Exception("File is large");
        } else {
            if ($encode) {
                $filename = md5(uniqid()) . "." . $fileExtension;
            }
            $file = $dir . basename($filename);
            if (move_uploaded_file($tmp, $file)) {
                return $filename;
            } else {
                throw new Exception("Could not upload file");
            }
        }
        return FALSE;
    }
}
