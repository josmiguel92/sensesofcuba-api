<?php

namespace App\Form;

use App\Entity\SocImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SocImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('title', null, [
//                'required' => false
//            ])
//            ->add('imageName')
//            ->add('imageSize')
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('imageFile', VichImageType::class,  [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SocImage::class,
        ]);
    }
}
