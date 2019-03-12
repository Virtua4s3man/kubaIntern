<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class)
            ->add('year')
            ->add('country', CountryType::class)
            ->add('available')
            ->add(
                'author',
                EntityType::class,
                [
                    'class' => Author::class,
                    'choice_label' => function ($author) {
                        return $author->getAuthorDisplay();
                    }
                ]
            )
            ->add(
                'genre',
                EntityType::class,
                [
                    'class' => Genre::class,
                    'choice_label' => 'name',
                ]
            )
            ->add('cover', ImageType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
