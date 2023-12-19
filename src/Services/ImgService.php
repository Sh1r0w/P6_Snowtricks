<?php 

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImgService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function addImg(
        UploadedFile $picture, 
        ?string $folder = '', 
        ?int $width = 250, 
        ?int $height = 250
        )
    {
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        $pictureInfos = getimagesize($picture);

        if($pictureInfos === false) {
            throw new Exception('Format incorrect');
        }

        switch ($pictureInfos['mime']){
            case 'image/png':
                $pictureSource = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $pictureSource = imagecreatefromwebp($picture);
                break;
            default:
            throw new Exception('Format incorrect');
        }

        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        switch ($imageWidth <=> $imageHeight){
            case -1:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;    
            case 0:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1:
                $squareSize = $imageWidth;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        $resizeImg = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizeImg, $pictureSource, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        if(!file_exists($path . '/mini/')){
            mkdir($path . '/mini/', 0755, true );
        }
        imagewebp($resizeImg, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    public function delete(
        string $fichier, 
        ?string $folder = '', 
        ?int $width = 300, 
    ?int $height = 300
    )
    {
        if($fichier !== 'default.webp'){
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/'  . $width . 'x' . $height . '-' . $fichier;
            if(file_exists($mini)){
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;
            if(file_exists($original)){
                unlink($original);
                $success = true;
            }

            if (is_dir($path)) {

                $files = glob($path . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
        

                if (rmdir($path . '/mini/') && rmdir($path)) {
                    $success = true;
                    
                } else {

                    $success = false;
                }
            }
    }
}
}