<?php


namespace App\Utilities;

use Slim\Http\UploadedFile;

class Upload
{


    public function uploadImage(UploadedFile $uploadedFile, $directory = ROOT.'public/uploads/images'){


        if ($uploadedFile->getError() === UPLOAD_ERR_OK){

            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            $targetPath = $directory . DIRECTORY_SEPARATOR. $filename;

            $uploadedFile->moveTo($targetPath);

            return $filename;
        }
    }


}