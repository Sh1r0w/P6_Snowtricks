<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Image;
use App\Entity\Connect;

class ImgService extends AbstractController
{

    /**
     * The function is a constructor that initializes a private property and assigns a value to it.
     */
    public function __construct(
        private ParameterBagInterface $params
        )
    {
        $this->params = $params;
        $this->path = $this->params->get('images_directory');
    }

    /**
     * The function `addImg` takes an uploaded image file, resizes it to a specified width and height,
     * saves the resized image and the original image to specified folders, and returns the filename of
     * the resized image.
     * 
     * @param UploadedFile picture The "picture" parameter is an instance of the UploadedFile class,
     * which represents an uploaded file in PHP. It is used to store the uploaded image file.
     * @param folder The "folder" parameter is used to specify the directory where the image will be
     * stored. It is an optional parameter and if not provided, the image will be stored in the root
     * directory.
     * @param width The width parameter is used to specify the desired width of the resized image.
     * @param height The `height` parameter is an optional parameter that specifies the desired height
     * of the resized image. If no value is provided, the default height is set to 250 pixels.
     * 
     * @return the filename of the uploaded image.
     */
    public function addImg(
        UploadedFile $picture,
        ?string $folder = '',
        ?int $width = 250,
        ?int $height = 250
    ) {
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        $pictureInfos = getimagesize($picture);

        if ($pictureInfos === false) {
            $this->addFlash(
               'error',
               'Format incorrect'
            );
        }else{

        switch ($pictureInfos['mime']) {
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
            $this->addFlash(
                'error',
                'Format incorrect'
             );
        }
    }

        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        switch ($imageWidth <=> $imageHeight) {
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

        

        if (!file_exists($this->path . $folder . '/mini/')) {
            mkdir($this->path . $folder . '/mini/', 0755, true);
        }
        imagewebp($resizeImg, $this->path . $folder . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($this->path . $folder . '/', $fichier);

        return $fichier;
    }

    /**
     * The function deletes a file and its corresponding mini version, as well as the entire folder if
     * it is empty.
     * 
     * @param string fichier The name of the file to be deleted.
     * @param folder The folder parameter is optional and specifies the folder where the file is
     * located. If no folder is specified, the file will be located in the root directory.
     * @param width The width parameter is an optional integer that specifies the desired width of the
     * image. If provided, it will be used to construct the file path for the mini version of the
     * image.
     * @param height The height parameter is an optional integer that specifies the desired height of
     * the image. If provided, it will be used to construct the file path for the mini version of the
     * image.
     */
    public function delete(
        string $fichier,
        ?string $folder = '',
        ?int $width = 300,
        ?int $height = 300
    ): void {
        if ($fichier !== 'default.webp') {
            $success = false;

            $mini = $this->path . $folder . '/mini/' . $width . 'x' . $height . '-' . $fichier;
            $original = $this->path . $folder . '/' . $fichier;


            if (file_exists($mini) && is_file($original)) {
                unlink($mini);
                unlink($original);
                $success = true;
            }

            if (is_dir($this->path)) {

                $files = glob($this->path . $folder . '/*');
                $mini = glob($this->path . $folder . '/mini/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }

                }

                foreach ($mini as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                if (rmdir($this->path . $folder . '/mini/') && rmdir($this->path . $folder)) {
                    $success = true;

                } else {

                    $success = false;
                }
            }
        }
    }

    /**
     * The function deletes an image file and its corresponding thumbnail from a specified folder.
     * 
     * @param Image image The `` parameter is an instance of the `Image` class, which represents
     * an image file. It is used to get the name of the image file to be deleted.
     * @param string folder The "folder" parameter is a string that represents the directory where the
     * image is stored.
     * @param width The width parameter is an optional integer that specifies the desired width of the
     * image. If not provided, it defaults to 300 pixels.
     * @param height The height parameter is an optional integer that specifies the desired height of
     * the image. If not provided, it defaults to 300 pixels.
     * 
     * @return Response a Response object with a success message and a HTTP status code of 200 (OK).
     */
    public function deleteOne(Image $image,string $folder, ?int $width = 300,
    ?int $height = 300): Response  {

        $mini = $this->path . $folder . '/mini/' . $width . 'x' . $height . '-' . $image->getName();
        $original = $this->path . $folder . '/' . $image->getName();

        if (file_exists($mini) && is_file($original)) {
            unlink($mini);
            unlink($original);
            
        }

        return new Response('Image supprimée avec succès', Response::HTTP_OK);
    }

    /**
     * The function deletes a user's profile image and returns a success message.
     * 
     * @param Connect connect An instance of the Connect class, which contains information about the
     * user's connection and image details.
     * @param width The width parameter is an optional integer that specifies the desired width of the
     * image to be deleted. If not provided, it defaults to 250 pixels.
     * @param height The height parameter is an optional integer that specifies the height of the image
     * to be deleted. If no value is provided, the default height is set to 250 pixels.
     * 
     * @return Response a Response object with a message "Image supprimée avec succès" and a status
     * code of 200 (HTTP_OK).
     */
    public function deleteProfil(Connect $connect, ?int $width = 250,
    ?int $height = 250): Response {

        $mini = $this->path . $connect->getUsername() . '/mini/' . $width . 'x' . $height . '-' . $connect->getImguser();
        $original = $this->path . $connect->getUsername() . '/' . $connect->getImguser();

        if (file_exists($mini) && is_file($original)) {
            unlink($mini);
            unlink($original);
        }

        return new Response('Image supprimée avec succès', Response::HTTP_OK);
    }


    /**
     * The function renames a folder in PHP.
     * 
     * @param oldFolder The name of the folder that you want to rename.
     * @param newFolder The new name for the folder that you want to rename.
     */
    public function renameFolder($oldFolder, $newFolder){
        rename($this->path . $oldFolder, $this->path . $newFolder);
    }
}