<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-26
 * Time: 10:25
 */

namespace App\FOS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'locale',
            ChoiceType::class,
            [
                'choices' => [
                    'EN' => 'en',
                    'PL' => 'pl',
                ],
            ]
        );
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }
}