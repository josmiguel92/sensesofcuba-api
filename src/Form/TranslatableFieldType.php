<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatableFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['allowed_lang'] as $lang) {
            $builder
                ->add($lang, $options['formType'])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'formType' => TextType::class,
            'allowed_lang' => Product::ENABLED_LANGUAGES,
            // Configure your form options here
        ]);
        $resolver->setAllowedTypes('allowed_lang', ['null', 'array']);
        $resolver->setAllowedTypes('formType', ['string']);
    }
}
