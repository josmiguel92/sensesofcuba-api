<?php

namespace App\Form;

use App\Entity\TranslatedDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TranslatedDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enFile', SocFileType::class, ['label'=>'English Version file', 'required'=>false])
            ->add('esFile', SocFileType::class, ['label'=>'Spanish Version file', 'required'=>false])
            ->add('deFile', SocFileType::class, ['label'=>'German Version file', 'required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TranslatedDocument::class,
        ]);
    }
}
