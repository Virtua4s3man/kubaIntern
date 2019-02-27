<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-02-26
 * Time: 10:25
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale');
    }

    public function getParent()
    {
        return 'FosUserBundleFormTypeRegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}