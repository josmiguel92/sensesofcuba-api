<?php

namespace App\Form;

use App\Entity\ConfigValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('value')
        ;


//        foreach (ConfigValue::getMetadataFieldTypeList() as $field => $formType) {
//            $builder->add($field, $formType);
//        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConfigValue::class,
        ]);
    }
}
