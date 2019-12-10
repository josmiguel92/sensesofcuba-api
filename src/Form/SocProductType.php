<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\SocProduct;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\EntityTypeConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocProductType extends EasyAdminFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenceName', null)
            ->add('enabled', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'label' => "Enabled",
                'required' => false
            ])
            ->add('parent')
            ->add('file', SocFileType::class)
            ->add('image', SocImageType::class)
            ->add('translations', TranslationsType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SocProduct::class,
        ]);
    }
}
