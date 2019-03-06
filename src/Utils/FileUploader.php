<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-06
 * Time: 10:30
 */

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * @var string
     */
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $filename = md5(uniqid() . '.' . $file->guessExtension());
        $file->move($this->targetDirectory, $filename);

        return $filename;
    }
}
