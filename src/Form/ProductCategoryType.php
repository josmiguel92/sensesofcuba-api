<?php

namespace App\Form;

use App\Entity\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('imageFile', VichImageType::class, [
            ])
        ;

        foreach (ProductCategory::getMetadataFieldTypeList() as $field => $formType) {
            $builder->add($field, $formType);
        }

        foreach (ProductCategory::getTranslatableMetadataNames() as $field) {
            foreach (ProductCategory::getLangList() as $lang) {
                $builder->add(sprintf("%s:%s", $field, $lang));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCategory::class,
        ]);
    }
}
