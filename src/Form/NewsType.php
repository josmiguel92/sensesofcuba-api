<?php

namespace App\Form;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('referenceName')
            ->add('enabled', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'label' => "Enabled",
                'required' => false
            ])
            ->add('translations', TranslationsType::class)
            ->add(
                'isEnglishGlobalTranslation',
                CheckboxType::class,
                [
                    'label_attr' => ['class' => 'switch-custom'],
                    'label' => "Use english version as fallback",
                    'required' => false
                ]
            )
            ->add('translatedDocument', TranslatedDocumentType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
