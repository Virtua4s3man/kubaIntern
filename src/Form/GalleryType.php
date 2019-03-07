<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-06
 * Time: 11:21
 */

namespace App\Form;

use App\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends FileType
{
    private $imagePath;

    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addModelTransformer(new CallbackTransformer(
            function ($gallery = null) {
                if ($gallery instanceof \Traversable) {
                    $output = [];
                    foreach ($gallery as $image) {
                        $output[] = new File($this->imagePath . $image->getFile());
                    }
                    return $output;
                }
            },
            function ($uploadedFiles): ArrayCollection {
                if (count($uploadedFiles) > 0 and $uploadedFiles[0] instanceof UploadedFile) {
                    foreach ($uploadedFiles as &$file) {
                        $image = new Image();
                        $image->setFile($file);
                        $file = $image;
                    }
                    return new ArrayCollection($uploadedFiles);
                }
                return new ArrayCollection();
            }
        ));
    }

    public function getBlockPrefix()
    {
        return 'gallery';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'multiple' => true,
        ]);
    }
}
