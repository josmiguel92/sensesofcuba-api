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

        foreach (ProductCategory::getMetadataFieldTypeList() as $field => $form) {
            $builder->add($field, $form['type'] ?? null, $form['options'] ?? null);
        }

        foreach (ProductCategory::getTranslatableMetadataNames() as $field => $form) {
            $builder->add(
                $field,
                TranslatableFieldType::class,
                array_merge(
                    [
                    'allowed_lang' => ProductCategory::getLangList(),
                    'formType' => $form['type'],
                    ],
                    $form['options']
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCategory::class,
        ]);
    }
}
