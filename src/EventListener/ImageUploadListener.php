<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-06
 * Time: 10:38
 */

namespace App\EventListener;

use App\Entity\Image;
use App\Utils\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Image) {
            return;
        }
        $file = $entity->getFile();
        if ($file instanceof UploadedFile) {
            $filename = $this->uploader->upload($file);
            $entity->setFile($filename);
        }
    }
}
