<?php

namespace App\Form;

use App\Entity\Attachment;
use App\Entity\Product;
use App\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('active')
            ->add('priority')
            ->add(
                'parent',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_attr' => static function ($choice, $key, $value) {
                        return ['data-category' => $choice->getName() ];
                        },
//                    'attr' => ['data-name' => 'test']
                    'group_by' => function($choice, $key, $value) {
//                        if ($choice <= new \DateTime('+3 days')) {
//                            return 'Soon';
//                        }
                        /**
                         * @var Product $choice
                         */
                        return $choice->getCategory() ? $choice->getCategory()->getName() : '';
                    },
                    'required' => false
                ]
            )
            ->add('category', EntityType::class,[
                'class' => ProductCategory::class,
            ])
            ->add('attachmentCollection', CollectionType::class, [
                'entry_type' => AttachmentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'entry_options' => ['label' => false],
                'by_reference' => false
            ])
        ;

        foreach (Product::getMetadataFieldTypeList() as $field => $form) {
            $builder->add($field, $form['type'] ?? null, $form['options'] ?? []);
        }

        foreach (Product::getTranslatableMetadataNames() as $field => $form) {
            $builder->add(
                $field,
                TranslatableFieldType::class,
                array_merge(
                    [
                        'allowed_lang' => Product::getLangList(),
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
            'data_class' => Product::class,
        ]);
    }
}
