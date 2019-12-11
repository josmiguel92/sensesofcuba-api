<?php

namespace App\Form;

use App\Entity\SocFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SocFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('title', null,  [
//                'required' => false,
//
//            ])
//            ->add('fileName')
//            ->add('fileSize')
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('file', VichFileType::class,  [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SocFile::class,
        ]);
    }
}
