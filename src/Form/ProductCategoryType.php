<?php

namespace App\Form;

use App\Entity\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active')
            ->add('name')
            ->add('imageFile', VichImageType::class, [
                'required' => false
            ])
            ->add('priority', IntegerType::class, [
                'attr' => ['min' => 0, 'max' => 100],
                'required' => false
            ])
        ;

        foreach (ProductCategory::getMetadataFieldTypeList() as $field => $formType) {
            $builder->add($field, $formType, ['required' => false]);
        }

        foreach (ProductCategory::getTranslatableMetadataNames() as $field => $formType) {
            $builder->add($field, TranslatableFieldType::class, [
                'allowed_lang' => ProductCategory::getLangList(),
                'formType' => $formType,
                'required' => false
            ]);
//
//            foreach (ProductCategory::getLangList() as $lang) {
//                $builder->add(sprintf("%s:%s", $field, $lang));
//            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCategory::class,
        ]);
    }
}
