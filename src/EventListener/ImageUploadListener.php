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
        $this->uploadFiles($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFiles($entity);
    }

    private function uploadFiles($entity)
    {
        if ($entity instanceof Image) {
            $this->uploadImage($entity);
        }

        if (is_array($entity) and $entity[0] instanceof Image) {
            $this->uploadGallery($entity);
        }
    }

    private function uploadGallery(array $entity)
    {
        foreach ($entity as &$image) {
            $this->uploadImage($image);
        }
    }

    private function uploadImage(Image &$entity)
    {
        $file = $entity->getFile();
        if ($file instanceof UploadedFile) {
            $filename = $this->uploader->upload($file);
            $entity->setFile($filename);
        }
    }
}
